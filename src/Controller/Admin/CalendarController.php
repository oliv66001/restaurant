<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Service\CalendarService;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/admin/calendar", name: "admin_calendar_")]
class CalendarController extends AbstractController
{


    #[Route("/", name: "index", methods: ["GET"])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $calendars = $calendarRepository->findAll();
        return $this->render('admin/calendar/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security, BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager,  CalendarService $calendarService): Response
    {
        $business_hours = $businessHoursRepository->findAll();


        $user = $security->getUser();
        $start = [];
        foreach ($business_hours as $hour) {
            if (!$hour->isClosed()) {
                // Heures d'ouverture du matin
                $openHour = (int)$hour->getOpenTime()->format('H');
                $closeHour = (int)$hour->getCloseTime()->format('H');

                for ($i = $openHour; $i < $closeHour; $i++) {
                    $start[] = $i;
                }

                $form = $this->createForm(CalendarType::class, $calendar, [
                    'hours' => $start,
                    'user' => $calendar->getName(), // ou n'importe quelle méthode pour obtenir l'utilisateur lié à cette réservation
                ]);
                // Heures d'ouverture du soir
                $openHourEvening = (int)$hour->getOpenTimeEvening()->format('H');
                $closeHourEvening = (int)$hour->getCloseTimeEvening()->format('H');

                for ($i = $openHourEvening; $i < $closeHourEvening; $i++) {
                    $start[] = $i;
                }
            } else {
                $this->addFlash('danger', 'Le restaurant est fermé ce jour.');
            }
        }
        // Récupération des places disponibles
        $numberOfGuests = $calendar->getNumberOfGuests() ?? 1;
        $remainingPlaces = $calendarService->getAvailablePlaces($calendar->getStart(), $numberOfGuests);

        $calendar->setAvailablePlaces($remainingPlaces);

        // Obtenir le nombre de places disponibles
        //$remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);

        // Si le nombre de places restantes est insuffisant, retourner une erreur
        if ($remainingPlaces < $numberOfGuests - $numberOfGuests) {
            $this->addFlash('danger', 'Désolé, il n’y a pas assez de places disponibles.');
            return $this->redirectToRoute('admin_calendar_index');
        }
        $form->handleRequest($request);

        $existingReservations = $calendarRepository->findByUserOrAll($user);

        if ($form->isSubmitted() && $form->isValid()) {
            // On démarre une transaction
            $entityManager->beginTransaction();

            try {
                // Mettons à jour $calendar avec les nouvelles valeurs du formulaire
                $start = $calendar->getStart();
                $dayOfWeek = (int) $start->format('N'); // 1 pour Lundi, 2 pour Mardi, etc.

                if ($dayOfWeek === 1) { // Si c'est un lundi
                    $this->addFlash('danger', 'Le restaurant est fermé . Veuillez choisir un autre jour.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('app_calendar_new');
                }


                $numberOfGuests = $calendar->getNumberOfGuests();

                // Obtenir le nombre de places disponibles
                $remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);

                // Si le nombre de places restantes est insuffisant, retourner une erreur
                if ($remainingPlaces < $numberOfGuests) {
                    $this->addFlash('danger', 'Désolé, il n’y a pas assez de places disponibles.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('app_calendar_new');
                }

                $calendar->setAvailablePlaces($remainingPlaces - $numberOfGuests);

                // Persister et flusher les données
                $entityManager->persist($calendar);
                $entityManager->flush();
                //dd($calendar);

                // Commit de la transaction
                $entityManager->commit();

                $emailAdmin = (new TemplatedEmail())
                    ->from('quai-antique@crocobingo.fr')
                    ->to('quai.antiquead@gmail.com')
                    ->subject('Réservation modifiée')
                    ->htmlTemplate('emails/modified_reservation.html.twig')
                    ->context([
                        'reservation' => $calendar,
                    ]);

                $emailUser = (new TemplatedEmail())
                    ->from('quai-antique@crocobingo.fr')
                    ->to($user->getEmail())
                    ->subject('Réservation modifiée')
                    ->htmlTemplate('emails/modifiedUser_reservation.html.twig')
                    ->context([
                        'reservation' => $calendar,
                    ]);

                $mailer->send($emailAdmin);
                $mailer->send($emailUser);

                $this->addFlash('success', 'La réservation a été modifiée avec succès.');


                return $this->redirectToRoute('admin_calendar_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $entityManager->rollback();
                throw $e;
            }
        }

        return $this->render('admin/calendar/edit.html.twig', [
            'calendar' => $calendar,
            'business_hours' => $business_hours,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security): Response
    {
        $user = $security->getUser();
        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $calendarRepository->remove($calendar, true);

            $emailAdmin = (new TemplatedEmail())
                ->from('quai-antique@crocobingo.fr')
                ->to('quai.antiquead@gmail.com')
                ->subject('Réservation supprimée')
                ->htmlTemplate('emails/deleted_reservation.html.twig')
                ->context([
                    'reservation' => $calendar,
                ]);

            $emailUser = (new TemplatedEmail())
                ->from('quai-antique@crocobingo.fr')
                ->to($user->getEmail())
                ->subject('Réservation supprimée')
                ->htmlTemplate('emails/deletedUser_reservation.html.twig')
                ->context([
                    'reservation' => $calendar,
                ]);

            $mailer->send($emailAdmin);
            $mailer->send($emailUser);
        }

        $this->addFlash('warning', 'La réservation a été supprimée avec succès.');

        return $this->redirectToRoute('admin_calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}

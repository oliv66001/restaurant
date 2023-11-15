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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route("/admin/calendar", name: "admin_calendar_")]
class CalendarController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        if (false === $this->isGranted('ROLE_DISHES_ADMIN', $calendarRepository)) {
            throw new AccessDeniedException('Seuls les super administrateurs peuvent accéder à cette page.');
        }
        $calendars = $calendarRepository->findAll();
        return $this->render('admin/calendar/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security, BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager,  CalendarService $calendarService): Response
    {
       
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function ($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        $openDays = [];
        foreach ($business_hours as $hour) {
            if (!$hour->isClosed()) {
                $openDays[] = $hour->getDay();
            }
        }
        if (empty($openDays)) {
            $this->addFlash('danger', 'Le restaurant est fermé pour vacance annuelle choisissez une autre date.');
            return $this->redirectToRoute('admin_calendar_index');
        }
        $randomOpenDay = $openDays[array_rand($openDays)];
        $randomOpenDayNameEnglish = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'][$randomOpenDay];
        $nextOpenDate = new \DateTime('next ' . $randomOpenDayNameEnglish);
        $currentDay = (int)(new \DateTime())->format('N');
        $daysUntilNextOpenDay = ($randomOpenDay - $currentDay + 7) % 7;
        $nextOpenDate = (new \DateTime())->add(new \DateInterval('P' . $daysUntilNextOpenDay . 'D'));

        $closedDays = $businessHoursRepository->findBy(['closed' => true]);
        $closedDaysArray = array_map(function ($hour) {
            return $hour->getDay();
        }, $closedDays);

        $user = $security->getUser();

        $start = [];
        foreach ($business_hours as $hour) {

            // Heures d'ouverture du matin
            $openHour = (int)$hour->getOpenTime()->format('H');
            $closeHour = (int)$hour->getCloseTime()->format('H');

            for ($i = $openHour; $i < $closeHour; $i++) {
                $start[] = $i;
            }

            // Heures d'ouverture du soir
            $openHourEvening = (int)$hour->getOpenTimeEvening()->format('H');
            $closeHourEvening = (int)$hour->getCloseTimeEvening()->format('H');

            for ($i = $openHourEvening; $i < $closeHourEvening; $i++) {
                $start[] = $i;
            }
        }

        // Récupération des places disponibles
        $numberOfGuests = $calendar->getNumberOfGuests() ?? 1;
        
        $remainingPlaces = $calendarService->getAvailablePlaces($calendar->getStart(), $numberOfGuests);

        $calendar->setAvailablePlaces($remainingPlaces);

        // Obtenir le nombre de places disponibles
        //$remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);

        $form = $this->createForm(CalendarType::class, $calendar, [
            'hours' => $start,

        ]);
        $form->handleRequest($request);

        $existingReservations = $calendarRepository->findByUserOrAll($user);
        if ($form->isSubmitted() && $form->isValid()) {
            // On démarre une transaction
            $entityManager->beginTransaction();

            try {
                // Mettons à jour $calendar avec les nouvelles valeurs du formulaire
                $start = $calendar->getStart();
                $dayOfWeek = (int) $start->format('N'); // 1 pour Lundi, 2 pour Mardi, etc.

                if ($dayOfWeek === $closedDays) {

                    $this->addFlash('danger', 'Le restaurant est fermé . Veuillez choisir un autre jour.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('admin_calendar_index');
                }
                
                $numberOfGuests = $calendar->getNumberOfGuests();

                // Obtenir le nombre de places disponibles
                $remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);

               // Si le nombre de places restantes est insuffisant, retourner une erreur
               if ($remainingPlaces < $numberOfGuests) {
                //dd($remainingPlaces, $numberOfGuests);
                $this->addFlash('danger', 'Désolé, il n’y a pas assez de places disponibles.');
                $entityManager->rollback();
                return $this->redirectToRoute('admin_calendar_index');
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
                if ($entityManager->getConnection()->isTransactionActive()) {
                    $entityManager->rollback();
                }
                throw $e;
            }
        }
        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'closedDaysArray' => $closedDaysArray,
            'business_hours' => $business_hours,
            'form' => $form,
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

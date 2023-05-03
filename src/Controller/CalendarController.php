<?php

namespace App\Controller;

use App\Entity\Users;
use IntlDateFormatter;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\UsersRepository;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]

    public function index(CalendarRepository $calendarRepository, BusinessHoursRepository $businessHoursRepository): Response
    {
        $business_hours = $businessHoursRepository->findAll();
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $calendars = $calendarRepository->findAll(); // L'administrateur voit toutes les réservations
        } else {
            $calendars = $calendarRepository->findByUserOrAll($this->getUser()); // Les autres utilisateurs ne voient que leurs réservations
        }

        return $this->render('calendar/index.html.twig', [
            'business_hours' => $business_hours,
            'calendars' => $calendars

        ]);
    }

    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CalendarRepository $calendarRepository, Security $security, MailerInterface $mailer, BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager): Response
    {
        $business_hours = $businessHoursRepository->findAll();
        $user = $security->getUser();
        $userId = $user->getId();
        // Récupérer les paramètres de la requête
        $numberOfGuests = $request->query->get('numberOfGuests');
        $start = $request->query->get('start');
        $remainingPlaces = $calendarRepository->getRemainingPlaces($numberOfGuests, $start);


        $calendar = new Calendar();
        $calendar->setAvailablePlaces(30);

        $calendar->setName($user);
        $calendar->setStart(new \DateTime());
        $form = $this->createForm(CalendarType::class, $calendar);

        $form->handleRequest($request);

        $existingReservations = $calendarRepository->findByUserOrAll($user);


        if ($form->isSubmitted() && $form->isValid()) {
            $numberOfGuests = $calendar->getNumberOfGuests();
            $occupiedPlaces = $calendarRepository->countOccupiedPlaces($calendar->getStart(), $numberOfGuests);
            $remainingPlaces = $calendar->getAvailablePlaces() - $occupiedPlaces;

            if ($numberOfGuests > 12) {
                $this->addFlash('danger', 'Vous ne pouvez pas réserver plus de 12 places.');
            } else if ($numberOfGuests > $remainingPlaces) {
                $this->addFlash('danger', 'Il ne reste pas suffisamment de places pour votre réservation.');
            } else {
                $entityManager = $this->managerRegistry->getManager();

                $entityManager->persist($calendar);
                $entityManager->flush();

                $emailAdmin = (new TemplatedEmail())
                    ->from('quai-antique@crocobingo.fr')
                    ->to('quai.antiquead@gmail.com')
                    ->subject('Nouvelle réservation créée')
                    ->htmlTemplate('emails/new_reservation.html.twig')
                    ->context([
                        'reservation' => $calendar,
                        'user' => $user,
                    ]);

                $emailUser = (new TemplatedEmail())
                    ->from('quai-antique@crocobingo.fr')
                    ->to($user->getEmail())
                    ->subject('Nouvelle réservation créée')
                    ->htmlTemplate('emails/newUser_reservation.html.twig')
                    ->context([
                        'reservation' => $calendar,
                        'user' => $user,
                    ]);

                $mailer->send($emailAdmin);
                $mailer->send($emailUser);

                $this->addFlash('success', 'La réservation a été enregistrée avec succès.');
                return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('calendar/new.html.twig', [
            'form' => $form->createView(),
            'button_label' => 'Enregistrer',
            'remainingPlaces' => $remainingPlaces,
            'existingReservations' => $existingReservations,
            'calendar' => $calendar,
            'availablePlaces' => $calendar->getAvailablePlaces(),
            'business_hours' => $business_hours,
        ]);
    }


    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar, BusinessHoursRepository $businessHoursRepository): Response
    {
        $business_hours = $businessHoursRepository->findAll();
        $dateFormatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT
        );
        $dateFormatter->setPattern('dd-MM-yyyy');
        $formattedStartDate = $dateFormatter->format($calendar->getStart());

        return $this->render('calendar/show.html.twig', [
            'business_hours' => $business_hours,
            'calendar' => $calendar,
            'formattedStartDate' => $formattedStartDate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security): Response
    {


        $remainingPlaces = 0;
        $calendar->setAvailablePlaces(30);

        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);
        $user = $security->getUser();

        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($calendar->getStart(), $calendar->getNumberOfGuests());
        $occupiedPlaces -= $calendar->getNumberOfGuests();
        $remainingPlaces = 30 - $occupiedPlaces;
        if ($remainingPlaces < 0) {
            $remainingPlaces = 0;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarRepository->save($calendar, true);



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


            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('calendar/edit.html.twig', [
            'remainingPlaces' => $remainingPlaces,
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
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

        $this->addFlash('success', 'La réservation a été supprimée avec succès.');

        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/remaining-places', name: 'app_calendar_remaining_places', methods: ['POST'])]
    public function setRemainingPlaces(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): JsonResponse
    {
        $calendar = $calendarRepository->find($request->get('calendar_id'));
    
        $restaurantCapacity = 30; // mettre la capacité de votre restaurant ici
        $calendarRepository->setRemainingPlaces($entityManager, $calendar);

        // Récupérer le nombre de places occupées pour la date et l'heure sélectionnées
        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($calendar->getStart(), $calendar->getNumberOfGuests());
    
        // Calculer le nombre de places disponibles
        $availablePlaces = max($restaurantCapacity - $occupiedPlaces, 0);
    
        $calendar->setAvailablePlaces($availablePlaces);
    
        // Mettre à jour le champ remainingPlaces en base de données
        $entityManager->flush();
    
        return $this->json(['remainingPlaces' => $availablePlaces]);
    }
    



    #[Route('/available-places', name: 'app_calendar_available_places')]
    public function getAvailablePlaces(Request $request, CalendarRepository $calendarRepository): JsonResponse
    {
        $start = new \DateTime($request->get('start'));
        $numberOfGuests = $request->get('numberOfGuests');

        // Nombre de places disponibles
        $capacity = 30; 

        // Récupérer le nombre de places occupées pour la date et l'heure sélectionnées
        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($start, $numberOfGuests);

        // Calculer le nombre de places disponibles
        $availablePlaces = max($capacity - $occupiedPlaces, 0);

        return $this->json(['remainingPlaces' => $availablePlaces]);
    }
}

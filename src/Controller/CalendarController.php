<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use DateTimeInterface;
use IntlDateFormatter;
use App\Entity\Calendar;
use App\Form\CalendarType;
use Psr\Log\LoggerInterface;
use App\Entity\BusinessHours;
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
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    const RESTAURANT_CAPACITY = 30;

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
    public function new(Request $request, CalendarRepository $calendarRepository, Security $security, MailerInterface $mailer, BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager, LoggerInterface $logger): Response

    {
        $hours = $businessHoursRepository->findAll();
        if (empty($hours)) {
            throw new \Exception('No business hours found');
        }
        foreach ($hours as $hour) {
            if (!$hour instanceof BusinessHours) {
                throw new \Exception('Invalid object in business hours: ' . get_class($hour));
            }
        }

        $business_hours = $businessHoursRepository->findAll();
        $user = $security->getUser();


        $hoursByDay = [];
        foreach ($business_hours as $hour) {
            $dayOfWeek = $hour->getDay(); // Assurez-vous d'avoir une méthode appropriée pour obtenir le jour de la semaine

            $openHour = null;
            if ($hour->getOpenTime() instanceof \DateTime) {
                $openHour = (int) $hour->getOpenTime()->format('H');
            }

            $closeHour = null;
            if ($hour->getCloseTime() instanceof \DateTime) {
                $closeHour = (int) $hour->getCloseTime()->format('H');
            }

            $hoursByDay[$dayOfWeek] = ['open' => $openHour, 'close' => $closeHour];
        }



        $dayOfWeek = (new DateTime())->format('N') - 1;

        $hoursByDay = [
            0 => [
                ['open' => 12, 'close' => 14],
                ['open' => 19, 'close' => 21]
            ],

        ];

        if (is_array($hoursByDay[$dayOfWeek])) {
            $hoursForToday = $hoursByDay[$dayOfWeek];
        } else {
            // Ajoutez une instruction de débogage ici
            dd('$hoursByDay[$dayOfWeek] is not an array');
        }


        $businessHours = $businessHoursRepository->findOneBy(['day' => $dayOfWeek]);

        if (!$businessHours) {
            // Gérer cette situation
            throw new \Exception("No business hours found for the specified day");
        }

        $hours = [];
        foreach ($hoursForToday as $timeRange) {
            if (isset($timeRange['open']) && isset($timeRange['close'])) {
                $hours = array_merge($hours, range($timeRange['open'], $timeRange['close']));
            } else {
                // Ajoutez une instruction de débogage ici
                dd("open or close doesn't exist or is not an integer");
            }
        }

        // Supprimer les doublons si nécessaire et trier le tableau
        $hours = array_unique($hours);
        sort($hours);



        if ($request->isXmlHttpRequest()) {
            // Si c'est une requête AJAX, on renvoie la réponse JSON
            $start = DateTime::createFromFormat('d/m/Y', $request->get('start'));
            $numberOfGuests = $request->query->get('numberOfGuests') ?? 0;

            $capacity = self::RESTAURANT_CAPACITY;
            $occupiedPlaces = $calendarRepository->countOccupiedPlaces($start, $numberOfGuests);

            $availablePlaces = max($capacity - $occupiedPlaces, 0);
            $logger->info('Appel de la méthode countOccupiedPlaces');
            $logger->info("Date de début : {$start->format('d-m-Y H:i:s')}");
            $logger->info("Nombre d'invités : {$numberOfGuests}");
            $logger->info("Places occupées : {$occupiedPlaces}");
            $logger->info("Places disponibles : {$availablePlaces}");


            return $this->json(['remainingPlaces' => $availablePlaces]);
        }

        $calendar = new Calendar();
        $calendar->setName($user);
        $calendar->setStart(new DateTime());
        $calendar->setBusinessHours($businessHours);

        // Récupérer le nombre de places disponibles pour la date et l'heure sélectionnées
        $start = $calendar->getStart();

        $capacity = self::RESTAURANT_CAPACITY;
        $numberOfGuests = $calendar->getNumberOfGuests() ?? 0;
        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($start, $numberOfGuests);
        $availablePlaces = max($capacity - $occupiedPlaces, 0);
        $calendar->setAvailablePlaces($availablePlaces);
        $form = $this->createForm(CalendarType::class, $calendar, ['hours' => $hours]);



        $form->handleRequest($request);

        $existingReservations = $calendarRepository->findByUserOrAll($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $numberOfGuests = $calendar->getNumberOfGuests();
            $occupiedPlaces = $calendarRepository->countOccupiedPlaces($calendar->getStart(), $numberOfGuests);
            $availablePlaces = $form->get('availablePlaces')->getData() - $occupiedPlaces;

            if ($numberOfGuests > 12) {
                $this->addFlash('danger', 'Vous ne pouvez pas réserver plus de 12 places.');
            } else if ($numberOfGuests > $availablePlaces) {
                $this->addFlash('danger', 'Il ne reste pas suffisamment de places pour votre réservation.');
            } else {
                // Mettre à jour la quantité de places disponibles
                $availablePlaces -= $numberOfGuests;
                $calendar->setAvailablePlaces($availablePlaces);

                $entityManager = $this->managerRegistry->getManager();
                $entityManager->persist($calendar);

                // Enregistrer la nouvelle quantité de places disponibles dans la base de données
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
            'existingReservations' => $existingReservations,
            'calendar' => $calendar,
            'availablePlaces' => $calendar->getAvailablePlaces(),
            'business_hours' => $business_hours,
        ]);
    }


    #[Route('/show/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar, BusinessHoursRepository $businessHoursRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        // Vérifier si l'utilisateur actuel est autorisé à accéder à cet objet Calendar
        if (!$authChecker->isGranted('view', $calendar)) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce calendrier.');
        }

        // Vérifier si l'objet Calendar est récupéré correctement
        if (!$calendar) {
            throw $this->createNotFoundException('Calendrier non trouvé.');
        }

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
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security, BusinessHoursRepository $businessHoursRepository): Response
    {
        $business_hours = $businessHoursRepository->findAll();

        $form = $this->createForm(CalendarType::class, $calendar);
        $remainingPlaces = $calendar->getNumberOfGuests();
        $user = $security->getUser();
        $form->handleRequest($request);


        $dayOfWeek = (new DateTime())->format('N') - 1;

        $hoursByDay = [
            0 => [
                ['open' => 12, 'close' => 14],
                ['open' => 19, 'close' => 21]
            ],

        ];
        if (is_array($hoursByDay[$dayOfWeek])) {
            $hoursForToday = $hoursByDay[$dayOfWeek];
        } else {
            // Ajoutez une instruction de débogage ici
            dd('$hoursByDay[$dayOfWeek] is not an array');
        }


        $businessHours = $businessHoursRepository->findOneBy(['day' => $dayOfWeek]);

        if (!$businessHours) {
            // Gérer cette situation
            throw new \Exception("No business hours found for the specified day");
        }

        $hours = [];
        foreach ($hoursForToday as $timeRange) {
            if (isset($timeRange['open']) && isset($timeRange['close'])) {
                $hours = array_merge($hours, range($timeRange['open'], $timeRange['close']));
            } else {
                // Ajoutez une instruction de débogage ici
                dd("open or close doesn't exist or is not an integer");
            }
        }

        // Supprimer les doublons si nécessaire et trier le tableau
        $hours = array_unique($hours);
        sort($hours);

        $form = $this->createForm(CalendarType::class, $calendar, ['hours' => $hours]); // Assure-toi que le formulaire utilise ces heures
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Pas de mise à jour des places disponibles
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
            'business_hours' => $business_hours,
            'calendar' => $calendar,
            'form' => $form,
            'hours' => $hours
        ]);
    }

    #[Route('/delete/{id}', name: 'app_calendar_delete', methods: ['POST'])]
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

    #[Route('/remaining-places-api', name: 'app_calendar_remaining_places', methods: ['POST', 'GET'])]
    public function remainingPlaces(Request $request, CalendarRepository $calendarRepository, LoggerInterface $logger): JsonResponse
    {
        $start = new DateTime($request->query->get('start'));
        $numberOfGuests = $request->query->get('numberOfGuests') ?? 0;

        $availablePlaces = $calendarRepository->getAvailablePlaces($start, $numberOfGuests);

        return new JsonResponse(['remainingPlaces' => $availablePlaces]);
    }


    #[Route('/available-places', name: 'app_calendar_available_places')]
    public function getAvailablePlaces(DateTimeInterface $start, int $numberOfGuests, CalendarRepository $calendarRepository): int
    {

        $availablePlaces = 30; // Définissez ici la capacité maximale de votre restaurant
        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($start, $numberOfGuests);

        $remainingPlaces = $availablePlaces - $occupiedPlaces;

        return $remainingPlaces;
    }
}

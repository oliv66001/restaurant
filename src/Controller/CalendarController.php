<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use DateTimeInterface;
use IntlDateFormatter;
use App\Entity\Calendar;
use App\Entity\Categories;
use App\Form\CalendarType;
use Psr\Log\LoggerInterface;
use App\Entity\BusinessHours;
use App\Service\CalendarService;
use App\Repository\UsersRepository;
use App\Repository\CalendarRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
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


    public function __construct(ManagerRegistry $managerRegistry, CalendarRepository $calendarRepository)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]

    public function index(CalendarRepository $calendarRepository, BusinessHoursRepository $businessHoursRepository, CategoriesRepository $categoriesRepository, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function ($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $calendars = $calendarRepository->findAll(); // L'administrateur voit toutes les réservations
        } else {
            $calendars = $calendarRepository->findByUserOrAll($this->getUser()); // Les autres utilisateurs ne voient que leurs réservations
        }

        return $this->render('calendar/index.html.twig', [
            'business_hours' => $business_hours,
            'categories' => $categories,
            'category' => $category,
            'calendars' => $calendars

        ]);
    }

    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        CalendarRepository $calendarRepository,
        Security $security,
        MailerInterface $mailer,
        BusinessHoursRepository $businessHoursRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        CalendarService $calendarService
    ): Response {

        $category = $entityManager->getRepository(Categories::class)->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function ($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        $user = $security->getUser();

        $openDays = [];
        foreach ($business_hours as $hour) {
            if (!$hour->isClosed()) {
                $openDays[] = $hour->getDay();
            }
        }
        if (empty($openDays)) {
            $this->addFlash('danger', 'Le restaurant est fermé pour vacance annuelle choisissez une autre date.');
            return $this->redirectToRoute('app_calendar_index');
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
       
        $calendar = new Calendar();
        $calendar->setName($user);
        $calendar->setStart(null);

        // Récupération des places disponibles
        $start = $calendar->getStart();

        $capacity = self::RESTAURANT_CAPACITY;
        $numberOfGuests = $calendar->getNumberOfGuests() ?? 0;
        
        $occupiedPlaces = $calendarRepository->countOccupiedPlaces($start, $numberOfGuests);
        //dd($start, $numberOfGuests);
        $availablePlaces = max($capacity - $occupiedPlaces, 0);
        $calendar->setAvailablePlaces($availablePlaces);
        $remainingPlaces = $calendarService->getAvailablePlaces($calendar->getStart(), $numberOfGuests);
       
        $calendar->setAvailablePlaces($remainingPlaces);
        

        //Récupération des heures d'ouverture du restaurant
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

        $start = array_unique($start);
        sort($start);

        $form = $this->createForm(CalendarType::class, $calendar, [
            'hours' => $start,

        ]);
        $form->handleRequest($request);

        $existingReservations = $calendarRepository->findByUserOrAll($user);

        if ($form->isSubmitted() && $form->isValid()) {
          
            // On démarre une transaction
            $entityManager->beginTransaction();

            try {
                $numberOfGuests = $calendar->getNumberOfGuests();
                $occupiedPlaces = $calendarRepository->countOccupiedPlaces($calendar->getStart(), $numberOfGuests);
                $availablePlaces = $form->get('availablePlaces')->getData() - $occupiedPlaces;
                
                // Mettons à jour $calendar avec les nouvelles valeurs du formulaire
                $start = $calendar->getStart();
                //dd($availablePlaces);
                //dd($start, $numberOfGuests);
                // Compte le nombre de réservations pour cette heure
                $count = $calendarRepository->countReservationsByHour($start);
                
                if ($count >= 30) {
                    $this->addFlash('error', 'Désolé, il y a déjà 30 réservations pour cette heure.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('app_calendar_new');
                }
                //dd($availablePlaces);
                // Obtenir le nombre de places disponibles
                $numberOfGuests = $calendar->getNumberOfGuests();
                $remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);
                 
                // Si le nombre de places restantes est insuffisant, retourner une erreur
                if ($remainingPlaces < $numberOfGuests) {
                    //dd($remainingPlaces, $numberOfGuests);
                    $this->addFlash('danger', 'Désolé, il n’y a pas assez de places disponibles.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('app_calendar_new');
                }

                $calendar->setAvailablePlaces($remainingPlaces - $numberOfGuests);
                
                
                $dayOfWeek = (int) $start->format('N'); // 1 pour Lundi, 2 pour Mardi, etc.

                if ($dayOfWeek === $closedDays) {

                    $this->addFlash('danger', 'Le restaurant est fermé . Veuillez choisir un autre jour.');
                    $entityManager->rollback();
                    return $this->redirectToRoute('app_calendar_new');
                }

              // Persister et flusher les données
                $entityManager->persist($calendar);
                $entityManager->flush();
                //dd($calendar);

                // Commit de la transaction
                $entityManager->commit();

                $logger->info('Reservation created successfully', ['remainingPlaces' => $remainingPlaces]);

                // ... (envoyer les emails)

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
                return $this->redirectToRoute('app_calendar_index');
            } catch (\Exception $e) {
                if ($entityManager->getConnection()->isTransactionActive()) {
                    $entityManager->rollback();
                }
                throw $e;
            }
        }


        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'business_hours' => $business_hours,
            'start' => $start,
            'category' => $category,
            'closedDaysArray' => $closedDaysArray,
            'remainingPlaces' => $remainingPlaces,
            'form' => $form->createView(),
            'existingReservations' => $existingReservations,
        ]);
    }


    #[Route('/show/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar, BusinessHoursRepository $businessHoursRepository, AuthorizationCheckerInterface $authChecker, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $category = $entityManager->getRepository(Categories::class)->findAll();

        // Vérifier si l'objet Calendar est récupéré correctement
        if (!$calendar) {
            throw $this->createNotFoundException('Calendrier non trouvé.');
        }

        // Vérifier si l'utilisateur actuel est autorisé à accéder à cet objet Calendar
        if ($authChecker->isGranted('ROLE_DISHES_ADMIN') || $authChecker->isGranted('ROLE_ADMIN')) {
            // Les utilisateurs avec le rôle DISHES_ADMIN ou ADMIN peuvent voir toutes les réservations
        } elseif ($authChecker->isGranted('ROLE_USER') && $calendar->getName() === $user) {
            // Les utilisateurs normaux peuvent seulement voir leurs propres réservations
        } else {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à accéder à ce calendrier.');
        }

        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function ($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        $dateFormatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT
        );
        $dateFormatter->setPattern('dd-MM-yyyy');
        $formattedStartDate = $dateFormatter->format($calendar->getStart());

        return $this->render('calendar/show.html.twig', [
            'business_hours' => $business_hours,
            'category' => $category,
            'calendar' => $calendar,
            'formattedStartDate' => $formattedStartDate,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository, MailerInterface $mailer, Security $security, BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager,  CalendarService $calendarService): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
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
            return $this->redirectToRoute('app_calendar_index');
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
                    return $this->redirectToRoute('app_calendar_new');
                }
                
                $numberOfGuests = $calendar->getNumberOfGuests();

                // Obtenir le nombre de places disponibles
                $remainingPlaces = $calendarService->getAvailablePlaces($start, $numberOfGuests);

               // Si le nombre de places restantes est insuffisant, retourner une erreur
               if ($remainingPlaces < $numberOfGuests) {
                //dd($remainingPlaces, $numberOfGuests);
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


                return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                if ($entityManager->getConnection()->isTransactionActive()) {
                    $entityManager->rollback();
                }
                throw $e;
            }
        }
        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'category' => $category,
            'closedDaysArray' => $closedDaysArray,
            'business_hours' => $business_hours,
            'form' => $form,
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

        $this->addFlash('warning', 'La réservation a été supprimée avec succès.');

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

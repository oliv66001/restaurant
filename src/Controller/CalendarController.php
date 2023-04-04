<?php

namespace App\Controller;

use App\Entity\Users;
use IntlDateFormatter;
use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\UsersRepository;
use App\Repository\CalendarRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            $calendars = $calendarRepository->findAll(); // L'administrateur voit toutes les réservations
        } else {
            $calendars = $calendarRepository->findByUserOrAll($this->getUser()); // Les autres utilisateurs ne voient que leurs réservations
        }
        
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendars

        ]);
    }

#[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
public function new(Request $request, CalendarRepository $calendarRepository, Security $security): Response
{
    $user = $security->getUser();
    $userId = $user->getId();
    $calendar = new Calendar();
    $calendar->setName($user);
    $form = $this->createForm(CalendarType::class, $calendar);
    $form->handleRequest($request);

    $existingReservations = $calendarRepository->findByUserOrAll($user);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier le nombre de réservations pour cette date et heure
        $reservationsCount = $calendarRepository->countReservationsAtDateTime($calendar->getStart());

        if ($reservationsCount >= 30) {
            // Ajoutez un message d'erreur et renvoyez à la page de réservation
            $this->addFlash('error', 'Désolé, il y a déjà 30 réservations pour cette date et heure. Veuillez choisir un autre créneau.');
            return $this->redirectToRoute('app_calendar_new');
        }

        // Enregistrez la réservation
        $calendarRepository->save($calendar, true);

        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('calendar/new.html.twig', [
        'calendar' => $calendar,
        'form' => $form->createView(),
        'existingReservations' => $existingReservations,
    ]);
}


    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        $dateFormatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT
        );
        $dateFormatter->setPattern('dd-MM-yyyy');
        $formattedStartDate = $dateFormatter->format($calendar->getStart());

        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
            'formattedStartDate' => $formattedStartDate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarRepository->save($calendar, true);

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $calendar->getId(), $request->request->get('_token'))) {
            $calendarRepository->remove($calendar, true);
        }

        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}

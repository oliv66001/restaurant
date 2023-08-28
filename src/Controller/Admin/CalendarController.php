<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/calendar", name: "admin_calendar_")]
class CalendarController extends AbstractController
{


    #[Route("/", name: "index", methods: ["GET"])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render('admin/calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }
    
    #[Route("/edit/{id}", name: "edit", methods: ["GET", "POST"])]
    public function edit(
        Request $request,
        Calendar $calendar,
        CalendarRepository $calendarRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_calendar_index');
        }

        return $this->render('admin/calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }
}

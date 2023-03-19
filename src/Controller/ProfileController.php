<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profil')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profil', methods: ['GET'])]
    public function index(Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'calendars' => $calendarRepository->findBy([
                'lastName' => $this->getUser(),
            ]),
        ]);
    }

    #[Route('/{id}', name: 'show' , methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        
        return $this->render('profile/index.html.twig', [
            'calendar' => $calendar,
            
        ]);
    }
}

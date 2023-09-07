<?php

namespace App\Controller\Admin;

use App\Repository\CalendarRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin', name: 'admin_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CalendarRepository $calendarRepository): Response
    {
        $calendars = $calendarRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }
   
}
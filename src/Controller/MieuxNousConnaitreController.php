<?php

namespace App\Controller;

use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MieuxNousConnaitreController extends AbstractController
{
    

    #[Route('/mieux/nous/connaitre', name: 'app_mieux_nous_connaitre')]
    public function index(BusinessHoursRepository $businessHoursRepository): Response
    {
        $business_hours = $businessHoursRepository->findAll();
        return $this->render('mieux_nous_connaitre/index.html.twig', [
            'controller_name' => 'MieuxNousConnaitreController',
            'business_hours' => $business_hours,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MentionLegalController extends AbstractController
{
   
    #[Route('/mention/legal', name: 'app_mention_legal')]
    public function index(BusinessHoursRepository $businessHoursRepository): Response
    {
        $business_hours = $businessHoursRepository->findAll();
        return $this->render('mention_legal/index.html.twig', [
            'controller_name' => 'MentionLegalController',
            'business_hours' => $business_hours,
        ]);
    }
}

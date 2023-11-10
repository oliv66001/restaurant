<?php

namespace App\Controller;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MentionLegalController extends AbstractController
{
   
    #[Route('/mention/legal', name: 'app_mention_legal')]
    public function index(BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                return $this->render('mention_legal/index.html.twig', [
            'controller_name' => 'MentionLegalController',
            'category' => $category,
            'business_hours' => $business_hours,
        ]);
    }
}

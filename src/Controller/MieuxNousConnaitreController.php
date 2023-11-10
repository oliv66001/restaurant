<?php

namespace App\Controller;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MieuxNousConnaitreController extends AbstractController
{
    

    #[Route('/mieux/nous/connaitre', name: 'app_mieux_nous_connaitre')]
    public function index(BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                return $this->render('mieux_nous_connaitre/index.html.twig', [
            'controller_name' => 'MieuxNousConnaitreController',
            'category' => $category,
            'business_hours' => $business_hours,
        ]);
    }
}

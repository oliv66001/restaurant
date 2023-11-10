<?php

namespace App\Controller;

use App\Entity\BusinessHours;
use App\Form\BusinessHoursType;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BusinessHoursController extends AbstractController
{
    private $businessHoursRepository;
    
    public function __construct(BusinessHoursRepository $businessHoursRepository)
    {
        $this->businessHoursRepository = $businessHoursRepository;
    }
   
    #[Route("/business/hours", name:"app_business_hours")]
    public function index(Request $request): Response
    {
        $hours = $this->businessHoursRepository->findAllOrderedByDay();
            
        usort($hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
    
        return $this->render('business_hours/index.html.twig', [
            'business_hours' => $hours,
        ]);
    }
}    

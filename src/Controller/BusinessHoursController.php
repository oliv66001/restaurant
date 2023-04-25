<?php

namespace App\Controller;

use App\Entity\BusinessHours;
use App\Form\BusinessHoursType;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/business-hours", name:"business_hours_")]
class BusinessHoursController extends AbstractController
{
    private $businessHoursRepository;
    
    public function __construct(BusinessHoursRepository $businessHoursRepository)
    {
        $this->businessHoursRepository = $businessHoursRepository;
    }

   
}

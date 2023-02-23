<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DrinkController extends AbstractController
{
    #[Route('/drink', name: 'app_drink')]
    public function index(): Response
    {
        return $this->render('drink/index.html.twig', [
            'controller_name' => 'DrinkController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MieuxNousConnaitreController extends AbstractController
{
    #[Route('/mieux/nous/connaitre', name: 'app_mieux_nous_connaitre')]
    public function index(): Response
    {
        return $this->render('mieux_nous_connaitre/index.html.twig', [
            'controller_name' => 'MieuxNousConnaitreController',
        ]);
    }
}

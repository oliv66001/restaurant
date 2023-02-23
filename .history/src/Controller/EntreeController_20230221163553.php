<?php

namespace App\Controller;

use App\Entity\Entree;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entree', name: 'app_entree_')]
class EntreeController extends AbstractController
{
    #[Route('/', name: 'app_entree')]
    public function index(): Response
    {
        return $this->render('entree/index.html.twig', [
            'controller_name' => 'EntreeController',
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Entree $entree): Response
    {
        return $this->render('entree/details.html.twig', compact('entree'));
    }
}

<?php

namespace App\Controller;

use App\Entity\Dishes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/plats', name: 'app_dishes_')]
class DishesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('dishes/index.html.twig', [
            'controller_name' => 'DishesController',
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Dishes $dishe): Response
    {
        return $this->render('dishes/details.html.twig', compact('dishe'));
    }
}

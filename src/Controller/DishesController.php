<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Categorie;
use App\Entity\Categories;
use App\Repository\DishesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/plats', name: 'app_dishes_')]
class DishesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        
        return $this->render('dishes/index.html.twig');
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Dishes $dishe
    ): Response
    {
        return $this->render('dishes/details.html.twig', compact('dishe'));
    }
}

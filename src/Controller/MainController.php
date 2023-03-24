<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Categories;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(CategoriesRepository $categoriesRepository, DishesRepository $dishesRepository, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $dishes = $dishesRepository->findAll();
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
      
        return $this->render('main/index.html.twig', compact('dishes', 'categories', 'category'));
       
            
    }
}

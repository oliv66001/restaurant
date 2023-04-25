<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Categories;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/plats', name: 'app_dishes_')]
class DishesController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository,DishesRepository $dishesRepository, EntityManagerInterface $entityManager, BusinessHoursRepository $businessHoursRepository): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        return $this->render('dishes/index.html.twig', compact('dishes', 'category', 'business_hours'));
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Dishes $dishe
    ): Response
    {
        return $this->render('dishes/details.html.twig', compact('dishe', 'business_hours'));
    }
}

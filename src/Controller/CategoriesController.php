<?php

namespace App\Controller;


use App\Entity\Categories;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
   
     #[Route('/{slug}', name: 'list')]
    public function list(Categories $categorie, Request $request, CategoriesRepository $categoriesRepository, DishesRepository $dishesRepository, EntityManagerInterface $entityManager, BusinessHoursRepository $businessHoursRepository
    ): Response
    {

        $business_hours = $businessHoursRepository->findAll();
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $dishes = $dishesRepository->findAll();
        $page = $request->query->getInt('page', 1);
        $dishes = $dishesRepository->findDishesPaginated($page, $categorie->getSlug(), 3);
        return $this->render('categories/list.html.twig', compact('categorie', 'dishes', 'category', 'business_hours'));
    }
}

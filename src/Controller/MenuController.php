<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Categories;
use App\Repository\MenuRepository;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    #[Route("/menus/{page}", name: "menu_index", requirements: ['page' => '\d+'])]
    public function index(
        MenuRepository $menuRepository,
        BusinessHoursRepository $businessHoursRepository,
        DishesRepository $dishesRepository,
        EntityManagerInterface $entityManager,
        CategoriesRepository $categoriesRepository,
        int $page = 1
    ): Response {

        $menusPerPage = 2; // Nombre de menus à afficher par page
        $menus = $menuRepository->findWithPagination($page, $menusPerPage); // Récupère les menus paginés
        $sortedMenus = [];

        foreach ($menus as $menu) {
            $sortedDishes = $menu->getDishes()->toArray();
            usort($sortedDishes, function($a, $b) {
                return $a->getCategories()->getCategoryOrder() <=> $b->getCategories()->getCategoryOrder();
            });
            $sortedMenus[] = [
                'menu' => $menu,
                'sortedDishes' => $sortedDishes,
            ];
        }
        
        $menus = $menuRepository->findAll();
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $dishes = $dishesRepository->findAll();
        $totalMenus = $menuRepository->count([]); // Récupère le nombre total de menus
        $totalPages = ceil($totalMenus / $menusPerPage); // Calcule le nombre total de pages
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        
        return $this->render('menu/index.html.twig', [
            'business_hours' => $business_hours,
            'menus' => $menus,
            'dishes' => $dishes,
            'sortedMenus' => $sortedMenus,
            'category' => $category,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/menu/{id}', name: 'menu_show', requirements: ['id' => '\d+'])]
    public function show(Menu $menu, BusinessHoursRepository $businessHoursRepository, CategoriesRepository $categoriesRepository, EntityManagerInterface $entityManager, MenuRepository $menuRepository, int $page = 1): Response
    {
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        $menus = $menuRepository->findAll();
        $sortedMenus = [];

        foreach ($menus as $aMenu) {
            $sortedDishes = $aMenu->getDishes()->toArray();
            usort($sortedDishes, function($a, $b) {
                return $a->getCategories()->getCategoryOrder() <=> $b->getCategories()->getCategoryOrder();
            });
            $sortedMenus[] = [
                'menu' => $aMenu, 
                'sortedDishes' => $sortedDishes,
            ];
        }
                $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        return $this->render('menu/show.html.twig', [
            'business_hours' => $business_hours,
            'categories' => $categories,
            'sortedMenus' => $sortedMenus,
            'category' => $category,
            'menu' => $menu,
        ]);
    }
}

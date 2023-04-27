<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MenuController extends AbstractController
{
    #[Route("/menus/{page}", name:"menu_index", requirements: ['page' => '\d+'])]
    public function index(MenuRepository $menuRepository, BusinessHoursRepository $businessHoursRepository, int $page = 1): Response
{
    $menusPerPage = 1; // Nombre de menus à afficher par page
    $menus = $menuRepository->findWithPagination($page, $menusPerPage); // Récupère les menus paginés
    $totalMenus = $menuRepository->count([]); // Récupère le nombre total de menus
    $totalPages = ceil($totalMenus / $menusPerPage); // Calcule le nombre total de pages
    $business_hours = $businessHoursRepository->findAll();
    
    return $this->render('menu/index.html.twig', [
        'business_hours' => $business_hours,
        'menus' => $menus,
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ]);
    
}

#[Route('/menu/{id}', name: 'menu_show', requirements: ['id' => '\d+'])]
public function show(Menu $menu, BusinessHoursRepository $businessHoursRepository): Response
{
    $business_hours = $businessHoursRepository->findAll();
    return $this->render('menu/show.html.twig', [
        'business_hours' => $business_hours,
        'menu' => $menu,
    ]);
}

}
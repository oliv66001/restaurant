<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Categories;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use App\Repository\HomePageImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(CategoriesRepository $categoriesRepository, DishesRepository $dishesRepository, EntityManagerInterface $entityManager, BusinessHoursRepository $businessHoursRepository): Response
    {

        $category = $entityManager->getRepository(Categories::class)->findAll();
        $dishes = $dishesRepository->findAll();
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        
        return $this->render('main/index.html.twig', [ 
            'business_hours' => $business_hours, 
            'dishes' => $dishes, 
            'categories' => $categories, 
            'category' => $category
        ]);
    }

    #[Route("/accept-cookies", name: "accept_cookies", methods: ["POST"])]

    public function acceptCookies(Request $request): JsonResponse
    {
        $request->getSession()->set('cookies_accepted', true);

        return new JsonResponse(['status' => 'success']);
    }

    #[Route("/refuse-cookies", name: "refuse_cookies", methods: ["POST"])]

    public function refuseCookies(Request $request): JsonResponse
    {
        $request->getSession()->set('cookies_refused', true);

        return new JsonResponse(['status' => 'success']);
    }
}

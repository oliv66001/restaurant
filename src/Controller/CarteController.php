<?php

namespace App\Controller;


use App\Entity\Categories;
use App\Repository\DishesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/carte')]
class CarteController extends AbstractController
{
   
    #[Route('/', name: 'app_carte')]

 
    public function index(
    EntityManagerInterface $entityManager,
    DishesRepository $dishesRepository,
    BusinessHoursRepository $businessHoursRepository
   
    ) : Response
    {
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/index.html.twig', 
            [
                'category' => $category,
                'dishes' => $dishes,
                'business_hours' => $business_hours
            ]);
    }

    #[Route('/menu', name: 'app_menu1')]
    public function menu(EntityManagerInterface $entityManager,
    DishesRepository $dishesRepository,
    BusinessHoursRepository $businessHoursRepository,
    CategoriesRepository $categoriesRepository) : Response
    {
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/menu1.html.twig', 
            [
                'category' => $category,
                'dishes' => $dishes,
                'business_hours' => $business_hours
            ]);
    }

    #[Route('/menu2', name: 'app_menu2')]
    public function menu2(EntityManagerInterface $entityManager,
    DishesRepository $dishesRepository,
    BusinessHoursRepository $businessHoursRepository,
    CategoriesRepository $categoriesRepository) : Response
    {
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/menu2.html.twig', 
            [
                'category' => $category,
                'dishes' => $dishes,
                'business_hours' => $business_hours
            ]);
    }

    #[Route('/menu3', name: 'app_menu3')]
    public function menu3(EntityManagerInterface $entityManager,
    DishesRepository $dishesRepository,
    BusinessHoursRepository $businessHoursRepository,
    CategoriesRepository $categoriesRepository) : Response
    {
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/menu3.html.twig', 
            [
                'category' => $category,
                'dishes' => $dishes,
                'business_hours' => $business_hours
            ]);
    }

    #[Route('/menu-template', name: 'app_menu_template')]
    public function menuTemplate(EntityManagerInterface $entityManager,
    DishesRepository $dishesRepository,
    BusinessHoursRepository $businessHoursRepository,
    CategoriesRepository $categoriesRepository) : Response
    {
        $dishes = $dishesRepository->findAll();
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
                $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/menu_template.html.twig', 
            [
                'category' => $category,
                'dishes' => $dishes,
                'business_hours' => $business_hours
            ]);
    }
}

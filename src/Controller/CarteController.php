<?php

namespace App\Controller;


use App\Entity\Categories;
use App\Repository\DishesRepository;
use Doctrine\ORM\EntityManagerInterface;
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
   
    ) : Response
    {
        $dishes = $dishesRepository->findAll();
      
        $category = $entityManager->getRepository(Categories::class)->findAll();
        
        return $this->render('carte/index.html.twig', compact(
            'category', 'dishes'));
    }

}

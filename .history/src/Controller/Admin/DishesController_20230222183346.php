<?php

namespace App\Controller\Admin;

use App\Entity\Dishes;
use App\Form\DishesFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/dishes', name: 'admin_dishes_')]
class DishesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/dishes/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Création d'un nouveau plat
        $dishes = new Dishes();

        // Création du formulaire
        $dishesForm = $this->createForm(DishesFormType::class, $dishes);

        return $this->renderForm('admin/dishes/add.html.twig',compact('dishesForm'));
       
            
    }


    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Dishes $dishes): Response
    {
        //Vérification si l'user peut éditer avec le voter
        $this->denyAccessUnlessGranted('DISHES_EDIT', $dishes);
        return $this->render('admin/dishes/index.html.twig');
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Dishes $dishes): Response
    {
        //Vérification si l'user peut supprimer avec le voter
        $this->denyAccessUnlessGranted('DISHES_DELETE', $dishes);
        return $this->render('admin/dishes/index.html.twig');
    }
}
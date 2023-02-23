<?php

namespace App\Controller\Admin;

use App\Entity\Dishes;
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
        return $this->render('admin/dishes/index.html.twig');
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Dishes $dishes): Response
    {
        return $this->render('admin/dishes/index.html.twig');
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Dishes $dishes): Response
    {
        return $this->render('admin/dishes/index.html.twig');
    }
}
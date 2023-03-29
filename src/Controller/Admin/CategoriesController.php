<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'ASC']);
        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajouter', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, CategoriesFormType $category, Categories $categories): Response
    {
        $category = new Categories();

        $form = $this->createForm(CategoriesFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès.');

            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route("/categories/edit/{id}", name:"edit", methods:["GET", "POST"])]

    public function edit(Request $request, Categories $category, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoriesFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie mise à jour avec succès.');

            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
            'categoryForm' => $form->createView(),
        ]);
    }
   
    
 #[Route("/categories/delete/{id}", name:"delete_categories", methods:["DELETE"])]
 
public function delete(Request $request, Categories $category, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);

    if ($this->isCsrfTokenValid('delete_categories' . $category->getId(), $data['_token'])) {
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès.');

        return new JsonResponse(['success' => true, 'message' => 'Catégorie supprimée avec succès'], 200);
    }

    // Échec de la suppression
    return new JsonResponse(['error' => 'Token invalide'], 400);
}

}
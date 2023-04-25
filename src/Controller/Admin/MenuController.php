<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Form\MenuFormType;
use App\Repository\MenuRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/menus', name: 'admin_menus_')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, MenuRepository $menuRepository): Response
    {
        $menus = $menuRepository->findAll();
        return $this->render('admin/menus/index.html.twig', compact('menus'));
    }

    #[Route('/ajouter', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, CategoriesRepository $categoriesRepository): Response
    {
        $menu = new Menu();

        $categories = $categoriesRepository->findAll();
        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu ajouté avec succès.');

            return $this->redirectToRoute('admin_menus_index');
        }

        return $this->render('admin/menus/add.html.twig', [
            'menuForm' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route("/edit/{id}", name:"edit", methods:["GET", "POST"])]

    public function edit(Request $request, Menu $menu, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Menu mis à jour avec succès.');

            return $this->redirectToRoute('admin_menus_index');
        }

        return $this->render('admin/menus/edit.html.twig', [
            'menu' => $menu,
            'menuForm' => $form->createView(),
        ]);
    }
   
    #[Route("/delete/{id}", name:"delete", methods:["DELETE"])]

    public function delete(Request $request, Menu $menu, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $data['_token'])) {
            $em->remove($menu);
            $em->flush();

            $this->addFlash('success', 'Menu supprimé avec succès.');

            return new JsonResponse(['success' => true, 'message' => 'Menu supprimé avec succès'], 200);
        }

        // Échec de la suppression
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}

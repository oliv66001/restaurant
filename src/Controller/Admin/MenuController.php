<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Entity\Dishes;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
        $dishes = $this->entityManager->getRepository(Dishes::class)->findAll();
        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);

        $groupedDishes = [];
        foreach ($dishes as $dish) {
            $category = $dish->getCategories()->getName();
            if (!isset($groupedDishes[$category])) {
                $groupedDishes[$category] = [];
            }
            $groupedDishes[$category][] = $dish;
        }

        // Créer et gérer le formulaire
        $menuForm = $this->createForm(MenuFormType::class);
        $menuForm->handleRequest($request);

        if ($menuForm->isSubmitted() && $menuForm->isValid()) {
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu ajouté avec succès.');

            return $this->redirectToRoute('admin_menus_index');
        }

        return $this->render('admin/menus/add.html.twig', [
            'menuForm' => $form->createView(),
            'groupedDishes' => $groupedDishes,
            'categories' => $categories,
        ]);
    }

    #[Route("/edit/{id}", name: "edit", methods: ["GET", "POST"])]

    public function edit(Request $request, Menu $menu, EntityManagerInterface $em, CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);
        $dishes = $this->entityManager->getRepository(Dishes::class)->findAll();
        $groupedDishes = [];
        foreach ($dishes as $dish) {
            $category = $dish->getCategories()->getName();
            if (!isset($groupedDishes[$category])) {
                $groupedDishes[$category] = [];
            }
            $groupedDishes[$category][] = $dish;
        }

       
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Menu mis à jour avec succès.');

            return $this->redirectToRoute('admin_menus_index');
        }

        return $this->render('admin/menus/edit.html.twig', [
            'menu' => $menu,
            'menuForm' => $form->createView(),
            'groupedDishes' => $groupedDishes,
            'categories' => $categories,
        ]);
    }

    #[Route("/delete/{id}", name: "delete_menu", methods: ["DELETE"])]

    public function deleteMenu(Request $request, Menu $menu, EntityManagerInterface $em): JsonResponse
    {
         //Vérification si l'user peut éditer avec le voter
         $this->denyAccessUnlessGranted('ROLE_DISHES_ADMIN', $menu);
        $data = json_decode($request->getContent(), true);
     
        if ($this->isCsrfTokenValid('delete_menu' . $menu->getId(), $data['_token'])) {
          
            $em->remove($menu);
            $em->flush();
            
            $this->addFlash('success', 'Menu supprimé avec succès.');

            return new JsonResponse(['success' => true, 'message' => 'Menu supprimé avec succès'], 200);
        }

        // Échec de la suppression
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
}

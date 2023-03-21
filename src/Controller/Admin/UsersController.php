<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\UsersFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/utilisateurs', name: 'admin_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findAll([], ['firstname', 'ASC'] );
        return $this->render('admin/users/index.html.twig', compact('users'));
    }

    #[Route('/edition{id}', name: 'edit')]
    public function edit(
        Users $users,
        Request $request, 
        EntityManagerInterface $em 
       ): Response
    {
        //Vérification si l'user peut éditer avec le voter
        $this->denyAccessUnlessGranted('USER_EDIT', $users);

        // Création du formulaire
        $usersForm = $this->createForm(UsersFormType::class, $users);

        $usersForm->handleRequest($request);

        //Vérification du soumission du formulaire
        if ($usersForm->isSubmitted() && $usersForm->isValid()) {

            $em->persist($users);
            $em->flush();


            //Message flash
            $this->addFlash('success', 'L\'utilisateur a bien été modifier');

            //Redirection vers la page de détails du produit
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'usersForm' => $usersForm->createView(),
            'users' => $users

        ]);
    }

#[Route('/suppression/{id}', name: 'delete')]
    public function delete(Users $users): Response
    {
        //Vérification si l'user peut supprimer avec le voter
        $this->denyAccessUnlessGranted('USER_EDIT', $users);

        return $this->render('admin/users/index.html.twig');
    }

}

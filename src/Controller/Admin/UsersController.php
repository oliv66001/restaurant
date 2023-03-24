<?php

namespace App\Controller\Admin;

use App\Entity\Users;
use App\Form\UsersFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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

   // Ajoutez l'argument UserRepository à votre méthode deleteUser
#[Route('/suppression/user/{id}', name: 'delete_user', methods: ['DELETE'])]
public function deleteUser(
    Request $request,
    EntityManagerInterface $em,
    int $id,
    UsersRepository $userRepository,
    LoggerInterface $logger // Ajoutez ceci
): JsonResponse {

    // Récupérer l'utilisateur à supprimer
    $userToDelete = $userRepository->find($id);
   

    if (!$userToDelete) {
        return new JsonResponse(['error' => 'Utilisateur introuvable'], 404);
    }

    $logger->info('User to delete: ' . $userToDelete->getId());

    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if ($this->isCsrfTokenValid('delete_user' . $userToDelete->getId(), $data['_token'])) {

       
        // On supprime le compte utilisateur de la base
        $em->remove($userToDelete);
            $em->flush();
    
            $this->addFlash('success', 'Compte utilisateur supprimé avec succès.');
    
            return new JsonResponse(['success' => true, 'message' => 'Compte utilisateur supprimé avec succès'], 200);
        }
    
        // Échec de la suppression
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
    

}

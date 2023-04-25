<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Calendar;
use App\Form\UsersFormType;
use App\Form\ProfileFormType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/profil', name: 'app_profil_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CalendarRepository $calendarRepository, BusinessHoursRepository $businessHoursRepository): Response
    {
        
        $business_hours = $businessHoursRepository->findAll();
        return $this->render('profile/index.html.twig', [
            'calendars' => $calendarRepository->findBy([
                'name' => $this->getUser(),
                'business_hours' => $business_hours,
            ]),
        ]);
    }

    #[Route('/edition/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        $id,
        Users $users,
        Request $request,
        EntityManagerInterface $em,
        UserInterface $currentUser
    ): Response {
        if ($users !== $currentUser) {
            // Vous pouvez renvoyer une erreur 403 ou rediriger l'utilisateur vers une autre page
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier le profil d\'un autre utilisateur.');
        }
        //Vérification si l'user peut éditer avec le voter
        $this->denyAccessUnlessGranted('USER_EDIT', $users);

        // Création du formulaire
        $profileForm = $this->createForm(ProfileFormType::class, $users);

        $profileForm->handleRequest($request);

        //Vérification du soumission du formulaire
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {

            $em->persist($users);
            $em->flush();


            //Message flash
            $this->addFlash('success', 'L\'utilisateur a bien été modifier');

            //Redirection vers la page de détails du produit
            return $this->redirectToRoute('app_main');
        }

        return $this->render('profile/edit.html.twig', [
            'business_hours' => $this->business_hours,
            'profileForm' => $profileForm->createView(),

        ]);
    }


    // Ajoutez l'annotation de la route en haut de votre méthode, en changeant le nom de la route et le chemin si nécessaire
    #[Route('/suppression/user/{id}', name: 'delete_user', methods:['DELETE'])]
    public function deleteUser(
        Request $request,
        EntityManagerInterface $em,
        UserInterface $user,  TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        // On vérifie si le token est valide
        if ($this->isCsrfTokenValid('delete_user' . $user->getId(), $data['_token'])) {
            // Déconnexion de l'utilisateur
            $tokenStorage->setToken(null);
        $session->invalidate();
    
            // On supprime le compte utilisateur de la base
            $em->remove($user);
            $em->flush();
    
            $this->addFlash('success', 'Compte utilisateur supprimé avec succès.');
    
            return new JsonResponse(['success' => true, 'message' => 'Compte utilisateur supprimé avec succès'], 200);
        }
    
        // Échec de la suppression
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }
    
    #[Route('/{id}', name: 'show' , methods: ['GET'])]
     public function show(Calendar $calendar): Response
     {
         
         return $this->render('profile/index.html.twig', [
             'calendar' => $calendar,
             
         ]);
     }
}

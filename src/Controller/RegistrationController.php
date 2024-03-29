<?php

namespace App\Controller;


use App\Entity\Users;
use App\Entity\Categories;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * Summary of RegistrationController
 */
class RegistrationController extends AbstractController
{
    
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, 
    UserPasswordHasherInterface $userPasswordHasher, 
    UserAuthenticatorInterface $userAuthenticator, 
    UsersAuthenticator $authenticator, 
    EntityManagerInterface $entityManager, 
    SendMailService $mail, 
    JWTService $jwt, 
    BusinessHoursRepository $businessHoursRepository): Response
    {
        $user = new Users();
        $category = $entityManager->getRepository(Categories::class)->findAll();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $business_hours = $businessHoursRepository->findAll();
        usort($business_hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            //génération du JWT de l'utilisateur
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            $payload = [
                'user_id' => $user->getId(),
            ];

            $token = $jwt->generate($header, $payload, 
            $this->getParameter('app.jwtsecret'));

    

            $mail->send(
                'quai-antique@crocobingo.fr',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                    compact('user','token') 
                
            );


            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'business_hours' => $business_hours,
            'category' => $category,
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'app_verify_user')]
    /**
     * Summary of verifyUser
     * @param mixed $token
     * @param JWTService $jwt
     * @param UsersRepository $userRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function verifyUser($token, JWTService $jwt, UsersRepository $userRepository, 
    EntityManagerInterface $em) : Response
    {
       //Vérification du token
           
       if($jwt->isValid($token) &&
        !$jwt->isExpired($token) &&
         $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            //Récupération du payload
            $payload = $jwt->getPayload($token);

            //Récupération de l'utilisateur
            $user = $userRepository->find($payload['user_id']);

            //Vérification d'un utilisateur actif avec compte non activé
            if ($user && !$user->getIsVerified()){
               $user->setIsVerified(true);
               $em->flush();
               $this->addFlash('success', 'Votre compte a bien été activé');
           return $this->redirectToRoute('app_profil_index');
            
            }
        }
       //Message d'erreur token
       else {
        $this->addFlash('danger', 'Le lien d\'activation est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
    
    }   

        #[Route('/renvoiverif', name: 'app_resend_verif')]
    public function resendVerif(
    Request $request, 
    UsersRepository $userRepository, 
    SendMailService $mail, 
    JWTService $jwt): Response
    {
        //Récupération de l'utilisateur
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }
       
               if ($user->getIsVerified()) {
                   $this->addFlash('danger', 'Votre compte est déjà activé');
                   return $this->redirectToRoute('app_profil_index');
               }

                //génération du JWT de l'utilisateur
                $header = [
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                ];

                $payload = [
                    'user_id' => $user->getId(),
                ];

                $token = $jwt->generate($header, $payload,

                $this->getParameter('app.jwtsecret'));

                 $mail->send(
                    'quai-antique@crocobingo.fr',
                    $user->getEmail(),
                    'Activation de votre compte',
                    'register',
                        compact ('user', 'token') 
                       );
                $this->addFlash('success', 'Un nouveau lien d\'activation vous a été envoyé par mail');
                return $this->redirectToRoute('app_profil_index');
}

}
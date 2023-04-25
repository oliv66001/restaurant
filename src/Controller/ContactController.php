<?php

namespace App\Controller;

use Exception;
use App\Entity\Contact;
use Psr\Log\LoggerInterface;
use App\Form\ContactFormType;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, SendMailService $sendMailService, LoggerInterface $logger, BusinessHoursRepository $businessHoursRepository): Response
    {
        
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);
        $session = $request->getSession();
        $business_hours = $businessHoursRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $em->persist($contact);
            $em->flush();

            try {
                $sendMailService->send(
                    'quai-antique@crocobingo.fr',
                    'quai.antiquead@gmail.com',
                    'Nouveau message de contact',
                    'contact',
                    ['contact' => $contact]
                );
                $this->addFlash('success', 'Votre message a bien été envoyé !');
            } catch (Exception $e) {
                $logger->error('Erreur lors de l\'envoi du courriel : ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur s\'est produite lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'business_hours' => $business_hours,
            'form' => $form->createView()
        ]);
    }
}

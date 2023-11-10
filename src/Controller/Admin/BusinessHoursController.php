<?php

namespace App\Controller\Admin;

use App\Entity\BusinessHours;
use App\Form\BusinessHoursType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BusinessHoursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route("/admin/business-hours", name: "admin_business_hours_")]
class BusinessHoursController extends AbstractController
{
    private $businessHoursRepository;

    private $entityManager;

    public function __construct(BusinessHoursRepository $businessHoursRepository, EntityManagerInterface $entityManager)
    {
        $this->businessHoursRepository = $businessHoursRepository;
        $this->entityManager = $entityManager;
    }


    #[Route("/", name: "index")]
    public function index(): Response
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Seuls les super administrateurs peuvent accéder à cette page.');
        }
        $hours = $this->businessHoursRepository->findAllOrderedByDay();
        
        usort($hours, function($a, $b) {
            $dayA = $a->getDay() === 0 ? 7 : $a->getDay();
            $dayB = $b->getDay() === 0 ? 7 : $b->getDay();
            return $dayA <=> $dayB;
        });
       
        return $this->render('admin/business_hours/index.html.twig',  [
            'hours' => $hours,
        ]);
    }


    // Ajoutez {id} à la route pour accepter l'identifiant en tant que paramètre
    #[Route("/edit/{id}", name: "edit", methods: ["GET", "POST"])]

    public function edit(Request $request, int $id): Response
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Seuls les super administrateurs peuvent modifier les horaires.');
        }
        // Utilisez le BusinessHoursRepository pour récupérer l'entité BusinessHours par ID
        $hours = $this->businessHoursRepository->find($id);

        // Vérifiez si l'entité BusinessHours existe, sinon affichez une erreur 404
        if (!$hours) {
            throw $this->createNotFoundException('Les heures d\'ouverture demandées n\'existent pas.');
        }

        $form = $this->createForm(BusinessHoursType::class, $hours);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_business_hours_index');
        }


        return $this->render('admin/business_hours/edit.html.twig', [
            'hours' => $hours,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/footer-data", name: "footer_data")]
    public function footerData(BusinessHoursRepository $businessHoursRepository)
    {
        $openingHours = $businessHoursRepository->findAll();

        return $this->render('_partials/_footer.html.twig', [
            'opening_hours' => $openingHours,
        ]);
    }
}

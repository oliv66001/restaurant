<?php

namespace App\Controller\Admin;

use App\Entity\BusinessHours;
use App\Form\BusinessHoursType;
use App\Repository\BusinessHoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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


    #[Route("/", name: "admin_business_hours_index")]

    public function index(): Response
    {
        $day = $this->businessHoursRepository->findAllOrderedByDay();
        $hours = $this->businessHoursRepository->findAll();
        return $this->render('admin/business_hours/index.html.twig',  [
            'day' => $day,
            'hours' => $hours,
        ]);
    }


    // Ajoutez {id} à la route pour accepter l'identifiant en tant que paramètre
    #[Route("/edit/{id}", name: "admin_business_hours_edit", methods: ["GET", "POST"])]

    public function edit(Request $request, int $id): Response
    {
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

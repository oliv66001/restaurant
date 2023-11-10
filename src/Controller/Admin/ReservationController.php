<?php

namespace App\Controller\Admin;

use DateTime;
use DateInterval;
use App\Entity\Users;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    #[Route('admin/reservation', name: 'admin_reservation')]
    public function index(
        CalendarRepository $calendar, 
        Request $request, 
        CsrfTokenManagerInterface $csrfTokenManager
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_DISHES_ADMIN');
        $events = $calendar->findAll();
        $rdvs = [];

        $token = $csrfTokenManager->refreshToken('calendar_token');
        foreach ($events as $event) {
            $startDate = $event->getStart();
            $endDate = clone $startDate;
            $endDate->add(new DateInterval('PT30M')); 
            // Ajoutez la durée de la réservation à la date de début

            $rdvs[] = [
                'id' => $event->getId(),
                'name' => $event->getName()->getId(),
                'start' => $startDate->format('Y-m-d\TH:i:s'),
                'end' => $endDate->format('Y-m-d\TH:i:s'),
                'title' => $event->getName()->getFirstName().' '.$event->getName()->getLastName(),
                'numberOfGuests' => $event->getNumberOfGuests(),
            ];
        }

        $data = json_encode($rdvs);
        return $this->render('admin/reservation/index.html.twig', [
            'data' => $data,
            'calendar_token' => $token
        ]);
    }
}

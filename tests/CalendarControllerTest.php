<?php

namespace App\Tests;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalendarControllerTest extends WebTestCase
{

public function testGetOccupiedPlacesForReservation()
{
    $client = static::createClient();

    $container = $client->getContainer();

    $entityManager = $container->get('doctrine.orm.entity_manager');

    $calendarRepository = $entityManager->getRepository(Calendar::class);

    // récupérer la première réservation
    $reservation = $calendarRepository->findOneBy([]);

    // compter le nombre de places occupées pour la première réservation
    $occupiedPlaces = $this->getOccupiedPlacesForReservation($reservation, $calendarRepository);

    $this->assertEquals($occupiedPlaces, $reservation->getNumberOfGuests());

    // récupérer toutes les réservations à la même date que la première réservation
    $reservationsAtDateTime = $calendarRepository->findBy(['start' => $reservation->getStart()]);

    // ajouter une nouvelle réservation à la même date
    $newReservation = new Calendar();
    $newReservation->setName($reservation->getName());
    $newReservation->setNumberOfGuests(7);
    $newReservation->setStart($reservation->getStart());

    $calendarRepository->save($newReservation);

    // vérifier que le nombre de places occupées pour la première réservation est correct
    $occupiedPlaces = $this->getOccupiedPlacesForReservation($reservation, $calendarRepository);

    $this->assertEquals($occupiedPlaces, $reservation->getNumberOfGuests() + $newReservation->getNumberOfGuests());

    // supprimer la nouvelle réservation
    $calendarRepository->remove($newReservation);

    $entityManager->flush();
}
}
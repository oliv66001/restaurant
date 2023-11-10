<?php

namespace App\Service;

use App\Repository\CalendarRepository;
use DateTimeInterface;

class CalendarService
{
    private $calendarRepository;

    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    public function getAvailablePlaces(?DateTimeInterface $start, int $numberOfGuests): int
    {
        $availablePlaces = 30;
        $occupiedPlaces = $this->calendarRepository->countOccupiedPlaces($start, $numberOfGuests);

        return $availablePlaces - $occupiedPlaces;
    }
}
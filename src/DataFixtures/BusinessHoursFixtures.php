<?php

namespace App\DataFixtures;

use App\Entity\BusinessHours;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class BusinessHoursFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // 0 pour Lundi, 1 pour Mardi, etc.
        $days = [0, 1, 2, 3, 4, 5, 6];

        foreach ($days as $day) {
            $businessHour = new BusinessHours();
            $businessHour->setDay($day);
            $businessHour->setOpenTime(new DateTime('12:00'));
            $businessHour->setCloseTime(new DateTime('15:00'));
            $businessHour->setOpenTimeEvening(new DateTime('19:00'));
            $businessHour->setCloseTimeEvening(new DateTime('23:00'));

            $manager->persist($businessHour);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\BusinessHours;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BusinessHoursFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $days = ['Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $openTime1 = '12:00';
        $closeTime1 = '14:00';
        $openTime2 = '19:00';
        $closeTime2 = '22:00';

        foreach ($days as $day) {
            $hour1 = new BusinessHours();
            $hour1->setDay($day);
            $hour1->setOpenTime($openTime1);
            $hour1->setCloseTime($closeTime1);
            
            $hour2 = new BusinessHours();
            $hour2->setDay($day);
            $hour2->setOpenTime($openTime2);
            $hour2->setCloseTime($closeTime2);

            $manager->persist($hour1);
            $manager->persist($hour2);
        }

        $manager->flush();
    }
}

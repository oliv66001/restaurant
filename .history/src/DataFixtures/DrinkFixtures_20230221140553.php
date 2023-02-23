<?php

namespace App\DataFixtures;

use App\Entity\Drink;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class DrinkFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($drk =1; $drk <= 5; $drk++){
            $drink = new Drink();
            $drink->setName($faker->name);
            $drink->setDescription($faker->text());
            $drink->setSlug($this->slugger->slug($drink->getName())->lower());
            $drink->setPrice(mt_rand(2, 60));


            $category = $this->getReference('category_'.mt_rand(1, 6));
            $drink->setCategories($category);
            $manager->persist($drink);

            $this->addReference('drinks_'.$drk, $drink);
        }

        $manager->flush();
    }
}

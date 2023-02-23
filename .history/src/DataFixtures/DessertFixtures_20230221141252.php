<?php

namespace App\DataFixtures;

use App\Entity\Dessert;
use App\Entity\Desserts;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class DessertFixtures extends Fixture
{

    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for($des =1; $des <= 5; $des++){
            $dessert = new Dessert();
            $dessert->setName($faker->name);
            $dessert->setDescription($faker->text());
            $dessert->setSlug($this->slugger->slug($dessert->getName())->lower());
            $dessert->setPrice(mt_rand(2, 60));


            $category = $this->getReference('category_'.mt_rand(1, 6));  
            $dessert->setCategories($category);   
            $manager->persist($dessert);

            $this->addReference('desserts_'.$des, $dessert);
         }

        $manager->flush();
    }
}

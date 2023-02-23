<?php

namespace App\DataFixtures;

use App\Entity\Dishes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class DishesFixtures extends Fixture
{

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void

        {
            $faker = \Faker\Factory::create('fr_FR');

         for($dis =1; $dis <= 100; $dis++){
            $dishes = new Dishes();
            $dishes->setName($faker->name(10));
            $dishes->setDescription($faker->text());
            $dishes->setSlug($this->slugger->slug($dishes->getName())->lower());
            $dishes->setPrice(mt_rand(10, 60));

            // Recherche d'une catégorie aléatoire
            $category = $this->getReference('category_'.mt_rand(1, 6));  
            $dishes->setCategories($category);     
            $manager->persist($dishes);



            $this->addReference('dishes_'.$dis, $dishes);
         }

        $manager->flush();
    }
}

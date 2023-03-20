<?php

namespace App\DataFixtures;

use App\Entity\Dishes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class DishesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        for($dis = 1; $dis <= 50; $dis++){
            $dishe = new Dishes();
            $dishe->setName($faker->text(15));
            $dishe->setDescription($faker->text());
            $dishe->setSlug($this->slugger->slug($dishe->getName())->lower());
            $dishe->setPrice($faker->numberBetween(13, 50));

            //On va chercher une référence de catégorie
            $category = $this->getReference('cat-'. mt_rand(1, 10));
            $dishe->setCategories($category);

            $this->setReference('dis-'.$dis, $dishe);
            $manager->persist($dishe);
        }

        $manager->flush();
    }
}

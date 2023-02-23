<?php

namespace App\DataFixtures;

use App\Entity\Plats;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class PlatsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($pla =1; $pla <= 5; $pla++){
            $plat = new Plats();
            $plat->setName($faker->name);
            $plat->setDescription($faker->text());
            $plat->setSlug($this->slugger->slug($plat->getName())->lower());
            $plat->setPrice(mt_rand(2, 60));

            $category = $this->getReference('category_'.mt_rand(1, 6));
            $plat->setCategories($category);
            $manager->persist($plat);
            $this->addReference('plats_'.$pla, $plat);
        }

        $manager->flush();
    }
}

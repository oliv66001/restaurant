<?php

namespace App\DataFixtures;

use App\Entity\Entree;
use App\Entity\Entrees;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class EntreeFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($ent =1; $ent <= 5; $ent++){
            $entree = new Entree();
            $entree->setName($faker->name);
            $entree->setDescription($faker->text());
            $entree->setSlug($this->slugger->slug($entree->getName())->lower());
            $entree->setPrice(mt_rand(2, 60));
            $manager->persist($entree);
        }

        $manager->flush();
    }
}

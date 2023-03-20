<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($img = 1; $img <= 100; $img++){
            $image = new Images();
            $image->setName($faker->image(null, 640, 480));
            $image->setImageUrl($faker->imageUrl(null, 640, 480, 'food'));
            $product = $this->getReference('dis-'.rand(1, 10));
            $image->setDishes($product);
            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DishesFixtures::class
        ];  
    }
}

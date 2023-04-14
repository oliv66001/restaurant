<?php

namespace App\DataFixtures;

use App\Entity\Dishes;
use App\Entity\Images;
use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\CategoriesFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class DishesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createDish('Soupe à l\'oignon', 'Une délicieuse soupe à l\'oignon gratinée', 5, 'cat-2', $manager);
        $this->createDish('Salade niçoise', 'Une salade niçoise fraîche et savoureuse', 8, 'cat-3', $manager);
        $this->createDish('Boeuf bourguignon', 'Un délicieux boeuf bourguignon à la sauce riche', 15, 'cat-5', $manager);
        $this->createDish('Fondue savoyarde', 'Une fondue savoyarde traditionnelle et onctueuse', 18, 'cat-6', $manager);
        $this->createDish('Tiramisu', 'Un dessert italien crémeux et léger', 7, 'cat-9', $manager);
        $this->createDish('Plateau de fromages', 'Un assortiment de fromages français', 12, 'cat-11', $manager);
        $this->createDish('Coca-Cola', 'Une boisson gazeuse rafraîchissante', 3, 'cat-13', $manager);
        $this->createDish('Vin rouge', 'Un verre de vin rouge délicat', 6, 'cat-14', $manager);

        $manager->flush();
    }

    public function createDish(
        string $name,
        string $description,
        int $price,
        string $categoryReference,
        ObjectManager $manager)
    {
        $dish = new Dishes();
        $dish->setName($name);
        $dish->setDescription($description);
        $dish->setSlug($name);
        $dish->setPrice($price);
        $dish->setCategories($this->getReference($categoryReference));
        
        // Add Images if needed (createImage() method should be created in this class)
        // $dish->addImage($this->createImage('image-url', $dish));

        $manager->persist($dish);
    }

    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class,
        ];
    }
}

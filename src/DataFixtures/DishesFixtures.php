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
        // Créez 5 plats pour chaque catégorie
        for ($i = 1; $i <= 5; $i++) {
            $this->createDish("Soupe à l'oignon $i", 'Une délicieuse soupe à l\'oignon gratinée', 5, 'cat-2', "dish-" . (($i-1)*9+1), $manager);
            $this->createDish("Salade niçoise $i", 'Une salade niçoise fraîche et savoureuse', 8, 'cat-3', "dish-" . (($i-1)*9+2), $manager);
            $this->createDish("Boeuf bourguignon $i", 'Un délicieux boeuf bourguignon à la sauce riche', 15, 'cat-5', "dish-" . (($i-1)*9+3), $manager);
            $this->createDish("Fondue savoyarde $i", 'Une fondue savoyarde traditionnelle et onctueuse', 18, 'cat-6', "dish-" . (($i-1)*9+4), $manager);
            $this->createDish("Tiramisu $i", 'Un dessert italien crémeux et léger', 7, 'cat-9', "dish-" . (($i-1)*9+5), $manager);
            $this->createDish("Plateau de fromages $i", 'Un assortiment de fromages français', 12, 'cat-11', "dish-" . (($i-1)*9+6), $manager);
            $this->createDish("Coca-Cola $i", 'Une boisson gazeuse rafraîchissante', 3, 'cat-13', "dish-" . (($i-1)*9+7), $manager);
            $this->createDish("Vin rouge $i", 'Un verre de vin rouge délicat', 6, 'cat-14', "dish-" . (($i-1)*9+8), $manager);
            $this->createDish("Glace $i", 'Une délicieuse glace parfumée', 4, 'cat-8', "dish-" . (($i-1)*9+9), $manager); // Ajouter des glaces à la catégorie des desserts
        }

        $manager->flush();
    }

    public function createDish(
        string $name,
        string $description,
        int $price,
        string $categoryReference,
        string $dishReference, // Ajoutez ce paramètre
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
        $this->addReference($dishReference, $dish);
    }

    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class,
        ];
    }
}

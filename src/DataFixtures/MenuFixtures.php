<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use App\Entity\Dishes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MenuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Créez vos objets Menu ici
        $menu1 = new Menu();
        $menu1->setName('Menu 1');
        $menu1->setDescription('Menu 1 description');
        $menu1->setPrice(25);

        $menu2 = new Menu();
        $menu2->setName('Menu 2');
        $menu2->setDescription('Menu 2 description');
        $menu2->setPrice(35);

        // Ajoutez les plats à vos menus
        for ($i = 1; $i <= 5; $i++) {
            $menu1->addDish($this->getReference("dish-{$i}"));
            $menu2->addDish($this->getReference("dish-{$i}"));
        }

        // Persistez les objets Menu
        $manager->persist($menu1);
        $manager->persist($menu2);

        // Exécutez les requêtes SQL pour créer les menus dans la base de données
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DishesFixtures::class,
        ];
    }
}

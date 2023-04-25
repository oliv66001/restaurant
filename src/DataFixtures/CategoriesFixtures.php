<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Entrées', null, 1, $manager);

        $this->createCategory('Entrées chaude', $parent, 2, $manager);
        $this->createCategory('Entrées froide', $parent, 3, $manager);


        $parent = $this->createCategory('Plats', null, 4, $manager);

        $this->createCategory('Plats chaud', $parent, 5, $manager);
        $this->createCategory('Fondues', $parent, 6, $manager);

        $parent = $this->createCategory('Desserts', null, 7, $manager);

        $this->createCategory('Glaces', $parent, 8, $manager);
        $this->createCategory('Gâteaux', $parent, 9, $manager);

        $parent = $this->createCategory('Plateau de fromage', null, 10, $manager);

        $this->createCategory('Plateau de fromage', $parent, 11, $manager);

        $parent = $this->createCategory('Boissons', null, 12, $manager);

        $this->createCategory('Boissons', $parent, 13, $manager);
        $this->createCategory('Vins et spiritieux', $parent, 14, $manager);

        $parent = $this->createCategory('Images d\'accueil', null, 15, $manager);

        $this->createCategory('Top image', $parent, 16, $manager);
        $this->createCategory('Bottom image', $parent, 17, $manager);
        $manager->flush();
    }

    public function createCategory(
        string $name,
        Categories $parent = null,
        int $order,
        ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $category->setCategoryOrder($order);
        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}

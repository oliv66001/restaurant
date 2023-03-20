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
        $parent = $this->createCategory('Entrées', null, $manager);
        
        $this->createCategory('Entrées chaude', $parent, $manager);
        $this->createCategory('Entrées froide', $parent, $manager);
        

        $parent = $this->createCategory('Plats', null, $manager);

        $this->createCategory('Plats chaud', $parent, $manager);
        $this->createCategory('Fondues', $parent, $manager);
        
        $parent = $this->createCategory('Desserts', null, $manager);

        $this->createCategory('Glaces', $parent, $manager);
        $this->createCategory('Gâteaux', $parent, $manager);
                
        $parent = $this->createCategory('Boissons', null, $manager);

        $this->createCategory('Boissons', $parent, $manager);
        $this->createCategory('Vins et spiritieux', $parent, $manager);
        $manager->flush();
    }

    public function createCategory(
        string $name, 
        Categories $parent = null, 
        ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $category->setCategoryOrder(rand(1, 10));
        $manager->persist($category);


        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}

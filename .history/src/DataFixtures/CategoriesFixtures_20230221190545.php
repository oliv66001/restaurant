<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{

    private $count = 1;
    public function __construct(private SluggerInterface $slugger)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory(name: 'entree', parent: null, manager: $manager);
  // $this->createCategory(name: 'Entrées chaudes', parent: $parent, manager: $manager);
  // $this->createCategory(name: 'Entrées froides', parent: $parent, manager: $manager);
            
        $parent = $this->createCategory(name: 'plats', parent: null, manager: $manager);
          //  $this->createCategory(name: 'Plats', parent: $parent, manager: $manager);

        $parent = $this->createCategory(name: 'desserts', parent: null, manager: $manager);
        //    $this->createCategory(name: 'Desserts', parent: $parent, manager: $manager);
        //    $this->createCategory(name: 'Plateaux de frommages', parent: $parent, manager: $manager);

        $parent = $this->createCategory(name: 'menus', parent: null, manager: $manager);
          //  $this->createCategory(name: 'Menu du midi', parent: $parent, manager: $manager);
          //  $this->createCategory(name: 'Menu du soir', parent: $parent, manager: $manager);
          //  $this->createCategory(name: 'Menu enfant', parent: $parent, manager: $manager);

        $parent = $this->createCategory(name: 'boissons', parent: null, manager: $manager);
           // $this->createCategory(name: 'Boissons', parent: $parent, manager: $manager);
           // $this->createCategory(name: 'vins et spiritieux', parent: $parent, manager: $manager);
         

        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager): Categories
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $category->setCategoryOrder($this->count);
        $manager->persist($category);

        $this->addReference('category_' . $this->count, $category);
        $this->count++;
        

        return $category;
    }
}

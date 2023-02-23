<?php

namespace App\Entity;

use App\Entity\MyTrait\SlugTrait;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[ORM\GeneratedValue]
    private ?int $categoryOrder;

    // Remet la base à zéro pour les tests datafixtures
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'categories')]
    private ?self $parent = null;
   

    #[ORM\Column(name: 'parent_id', nullable: true)]
    private ?int $parentId = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: Dishes::class)]
    private Collection $dishes;

    #[ORM\ManyToMany(targetEntity: Entree::class, mappedBy: 'categorieEntree')]
    private Collection $entrees;

    #[ORM\ManyToMany(targetEntity: Plat::class, mappedBy: 'categoriePlat')]
    private Collection $plats;

    #[ORM\ManyToMany(targetEntity: Dessert::class, mappedBy: 'categorieDessert')]
    private Collection $desserts;

    #[ORM\ManyToMany(targetEntity: Drink::class, mappedBy: 'categorieDrink')]
    private Collection $drinks;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->dishes = new ArrayCollection();
        $this->entrees = new ArrayCollection();
        $this->plats = new ArrayCollection();
        $this->desserts = new ArrayCollection();
        $this->drinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParentId(): ?int
{
    return $this->parentId;
}

public function setParentId(?int $parentId): self
{
    $this->parentId = $parentId;

    return $this;
}

    /**
     * @return Collection<int, self>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setParent($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dishes>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dishes $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes->add($dish);
            $dish->setCategories($this);
        }

        return $this;
    }

    public function removeDish(Dishes $dish): self
    {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getCategories() === $this) {
                $dish->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of categoryOrder
     */ 
    public function getCategoryOrder()
    {
        return $this->categoryOrder;
    }

    /**
     * Set the value of categoryOrder
     *
     * @return  self
     */ 
    public function setCategoryOrder($categoryOrder)
    {
        $this->categoryOrder = $categoryOrder;

        return $this;
    }

    /**
     * @return Collection<int, Entree>
     */
    public function getEntrees(): Collection
    {
        return $this->entrees;
    }

    public function addEntree(Entree $entree): self
    {
        if (!$this->entrees->contains($entree)) {
            $this->entrees->add($entree);
            $entree->addcategorieEntree($this);
        }

        return $this;
    }

    public function removeEntree(Entree $entree): self
    {
        if ($this->entrees->removeElement($entree)) {
            $entree->removecategorieEntree($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->addCategoriePlat($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): self
    {
        if ($this->plats->removeElement($plat)) {
            $plat->removeCategoriePlat($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Dessert>
     */
    public function getDesserts(): Collection
    {
        return $this->desserts;
    }

    public function addDessert(Dessert $dessert): self
    {
        if (!$this->desserts->contains($dessert)) {
            $this->desserts->add($dessert);
            $dessert->addCategorieDessert($this);
        }

        return $this;
    }

    public function removeDessert(Dessert $dessert): self
    {
        if ($this->desserts->removeElement($dessert)) {
            $dessert->removeCategorieDessert($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Drink>
     */
    public function getDrinks(): Collection
    {
        return $this->drinks;
    }

    public function addDrink(Drink $drink): self
    {
        if (!$this->drinks->contains($drink)) {
            $this->drinks->add($drink);
            $drink->addCategorieDrink($this);
        }

        return $this;
    }

    public function removeDrink(Drink $drink): self
    {
        if ($this->drinks->removeElement($drink)) {
            $drink->removeCategorieDrink($this);
        }

        return $this;
    }
}

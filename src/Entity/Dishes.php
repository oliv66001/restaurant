<?php

namespace App\Entity;

use App\Entity\Images;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\MyTrait\SlugTrait;
use App\Repository\DishesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DishesRepository::class)]

class Dishes
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide')]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractère',
        maxMessage: 'Le titre doit faire plus de {{ limit }} caractère'
    )]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou nul')]
    private $price;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'dishes')]
    #[ORM\JoinColumn(nullable: false)]
    private $categories;

    #[ORM\OneToMany(mappedBy: 'dishes', targetEntity: Images::class, orphanRemoval: true, cascade:['persist'])]
    private $images;

    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'dishes')]
    private Collection $menus;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->menus = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setDishes($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getDishes() === $this) {
                $image->setDishes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->addDish($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeDish($this);
        }

        return $this;
    }
}

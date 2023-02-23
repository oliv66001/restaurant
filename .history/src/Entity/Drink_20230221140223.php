<?php

namespace App\Entity;

use App\Entity\MyTrait\SlugTrait;
use App\Repository\DrinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DrinkRepository::class)]
class Drink
{
    use SlugTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'drinks')]
    private Collection $categorieDrink;

    public function __construct()
    {
        $this->categorieDrink = new ArrayCollection();
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

    /**
     * @return Collection<int, Categories>
     */
    public function getCategorieDrink(): Collection
    {
        return $this->categorieDrink;
    }

    public function addCategorieDrink(Categories $categorieDrink): self
    {
        if (!$this->categorieDrink->contains($categorieDrink)) {
            $this->categorieDrink->add($categorieDrink);
        }

        return $this;
    }

    public function removeCategorieDrink(Categories $categorieDrink): self
    {
        $this->categorieDrink->removeElement($categorieDrink);

        return $this;
    }
}

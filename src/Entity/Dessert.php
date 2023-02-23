<?php

namespace App\Entity;

use App\Entity\MyTrait\SlugTrait;
use App\Repository\DessertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DessertRepository::class)]
class Dessert
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

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'desserts')]
    private Collection $categorieDessert;

    public function __construct()
    {
        $this->categorieDessert = new ArrayCollection();
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
    public function getCategorieDessert(): Collection
    {
        return $this->categorieDessert;
    }

    public function addCategorieDessert(Categories $categorieDessert): self
    {
        if (!$this->categorieDessert->contains($categorieDessert)) {
            $this->categorieDessert->add($categorieDessert);
        }

        return $this;
    }

    public function removeCategorieDessert(Categories $categorieDessert): self
    {
        $this->categorieDessert->removeElement($categorieDessert);

        return $this;
    }
}

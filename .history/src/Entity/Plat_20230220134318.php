<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
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

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'plats')]
    private Collection $categoriePlat;

    public function __construct()
    {
        $this->categoriePlat = new ArrayCollection();
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
    public function getCategoriePlat(): Collection
    {
        return $this->categoriePlat;
    }

    public function addCategoriePlat(Categories $categoriePlat): self
    {
        if (!$this->categoriePlat->contains($categoriePlat)) {
            $this->categoriePlat->add($categoriePlat);
        }

        return $this;
    }

    public function removeCategoriePlat(Categories $categoriePlat): self
    {
        $this->categoriePlat->removeElement($categoriePlat);

        return $this;
    }
}

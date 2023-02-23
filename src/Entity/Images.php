<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Dishes::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private $dishes;

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

   

    /**
     * Get the value of dishes
     */ 
    public function getDishes(): ?Dishes
    {
        return $this->dishes;
    }

    /**
     * Set the value of dishes
     *
     * @return  self
     */ 
    public function setDishes($dishes): self
    {
        $this->dishes = $dishes;

        return $this;
    }
}

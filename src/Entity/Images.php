<?php

namespace App\Entity;

use App\Entity\Categories;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImagesRepository;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: true)]
    private $categories;

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
    public function getCategories(): ?categories
    {
        return $this->categories;
    }

    /**
     * Set the value of categories
     *
     * @return  self
     */ 
    public function setCategories($categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}

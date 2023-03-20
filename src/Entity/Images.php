<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $imageUrl;

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

    public function getDishes(): ?Dishes
    {
        return $this->dishes;
    }

    public function setDishes(?Dishes $dishes): self
    {
        $this->dishes = $dishes;

        return $this;
    }

    /**
     * Get the value of imageUrl
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set the value of imageUrl
     */
    public function setImageUrl($imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}

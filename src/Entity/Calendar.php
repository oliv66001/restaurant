<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'calendars', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Veuillez renseigner une date de début')]
    #[Assert\GreaterThanOrEqual(message: 'L\'heure de début doit être supérieure à l\'heure actuelle', value: 'now')]
    #[Assert\GreaterThan('today', message: 'La date de début doit être supérieure à la date du jour')]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez renseigner un nombre de personnes')]
    #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Le nombre de personnes doit être compris entre 1 et 12')]
    #[Assert\Positive(message: 'Le nombre de personnes doit être supérieur à 0')]
    private ?int $numberOfGuests = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?Users
    {
        return $this->name;
    }

    public function setName(?Users $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getNumberOfGuests(): ?int
    {
        return $this->numberOfGuests;
    }

    public function setNumberOfGuests(int $numberOfGuests): self
    {
        $this->numberOfGuests = $numberOfGuests;

        return $this;
    }

    
}

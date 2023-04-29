<?php

namespace App\Entity;

use App\Repository\BusinessHoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BusinessHoursRepository::class)]
class BusinessHours
{

    public const DAYS = [
    0 => 'Lundi',
    1 => 'Mardi',
    2 => 'Mercredi',
    3 => 'Jeudi',
    4 => 'Vendredi',
    5 => 'Samedi',
    6 => 'Dimanche'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\Column(length: 30)]
    private ?string $openTime = null;

    #[ORM\Column(length: 30)]
    private ?string $closeTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getOpenTime(): ?string
    {
        return $this->openTime;
    }

    public function setOpenTime(string $openTime): self
    {
        $this->openTime = $openTime;

        return $this;
    }

    public function getCloseTime(): ?string
    {
        return $this->closeTime;
    }

    public function setCloseTime(string $closeTime): self
    {
        $this->closeTime = $closeTime;

        return $this;
    }

    public function getDayName(): ?string
    {
        return isset(self::DAYS[$this->day]) ? self::DAYS[$this->day] : null;
    }
    


}

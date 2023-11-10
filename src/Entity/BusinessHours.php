<?php

namespace App\Entity;

use App\Repository\BusinessHoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BusinessHoursRepository::class)]
class BusinessHours
{
    public const DAYS = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        0 => 'Dimanche',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $day = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $openTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $closeTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $openTimeEvening = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTime $closeTimeEvening = null;

    #[ORM\Column(type: 'boolean')]
    private bool $closed = false;

    #[ORM\OneToMany(mappedBy: "businessHours", targetEntity: Calendar::class)]
    private $calendars;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getOpenTime(): ?\DateTime
    {
        return $this->openTime;
    }

    public function setOpenTime(\DateTime $openTime): self
    {
        $this->openTime = $openTime;

        return $this;
    }

    public function getCloseTime(): ?\DateTime
    {
        return $this->closeTime;
    }

    public function setCloseTime(\DateTime $closeTime): self
    {
        $this->closeTime = $closeTime;

        return $this;
    }

    public function getDayName(): ?string
    {
        if ($this->closed) {
            return 'Fermé';
        }

        return isset(self::DAYS[$this->day]) ? self::DAYS[$this->day] : null;
    }

    public function isOpen(): bool
    {
        return !$this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * Get the value of openTimeEvening
     *
     * @return ?\DateTime
     */
    public function getOpenTimeEvening(): ?\DateTime
    {
        return $this->openTimeEvening;
    }

    /**
     * Set the value of openTimeEvening
     *
     * @param ?\DateTime $openTimeEvening
     *
     * @return self
     */
    public function setOpenTimeEvening(?\DateTime $openTimeEvening): self
    {
        $this->openTimeEvening = $openTimeEvening;

        return $this;
    }

    /**
     * Get the value of closeTimeEvening
     *
     * @return ?\DateTime
     */
    public function getCloseTimeEvening(): ?\DateTime
    {
        return $this->closeTimeEvening;
    }

    /**
     * Set the value of closeTimeEvening
     *
     * @param ?\DateTime $closeTimeEvening
     *
     * @return self
     */
    public function setCloseTimeEvening(?\DateTime $closeTimeEvening): self
    {
        $this->closeTimeEvening = $closeTimeEvening;

        return $this;
    }

    /**
     * Get the value of calendars
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * Set the value of calendars
     */
    public function setCalendars($calendars): self
    {
        $this->calendars = $calendars;

        return $this;
    }
}

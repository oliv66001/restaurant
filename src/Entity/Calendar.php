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
    private ?\DateTimeInterface $start = null;

    #[ORM\Column]
    #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Le nombre de personnes doit être compris entre 1 et 12')]
    #[Assert\Positive(message: 'Le nombre de personnes doit être supérieur à 0')]
    private ?int $numberOfGuests = null;    

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $allergie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Regex(
        pattern:"/[<>.+\$%\/;:!?@€*-]/",
        match:false,
        message:"Vos allergies ne peuvent pas contenir de caractères spéciaux")]
    private ?string $allergieOfGuests = null;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(
        value : 30,
        message : 'Le nombre de places disponibles doit être supérieur ou égal à 1')]
    private ?int $availablePlaces = null;

    #[ORM\ManyToOne(targetEntity: BusinessHours::class, inversedBy: "calendars")]
    #[ORM\JoinColumn(name: "business_hours_id", referencedColumnName: "id", nullable: true)]
    private $businessHours;
    
    public function __construct() {
        $this->start = null;
        
    }
    
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

   public function setStart(?\DateTimeInterface $start): self
{
    $businessHours = $this->getBusinessHours();
    if ($businessHours !== null) {
        $openTime = $businessHours->getOpenTime();
        $closeTime = $businessHours->getCloseTime();

        if ($openTime instanceof \DateTime && $closeTime instanceof \DateTime) {
            $closeTime = \DateTime::createFromFormat('H:i:s', $closeTime->format('H:i:s'))->modify('-1 hour');

          // if ($start < $openTime || $start >= $closeTime) {
          //     throw new \InvalidArgumentException('La réservation doit être pendant les heures d\'ouverture.');
          // }
        }
    }

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

    

    /**
     * Get the value of allergie
     *
     * @return ?string
     */
    public function getAllergie(): ?string
    {
        return $this->allergie;
    }

    /**
     * Set the value of allergie
     *
     * @param ?string $allergie
     *
     * @return self
     */
    public function setAllergie(?string $allergie): self
    {
        $this->allergie = $allergie;

        return $this;
    }

    /**
     * Get the value of allergieOfGuests
     *
     * @return ?string
     */
    public function getAllergieOfGuests(): ?string
    {
        return $this->allergieOfGuests;
    }

    /**
     * Set the value of allergieOfGuests
     *
     * @param ?string $allergieOfGuests
     *
     * @return self
     */
    public function setAllergieOfGuests(?string $allergieOfGuests): self
    {
        $this->allergieOfGuests = $allergieOfGuests;

        return $this;
    }

    /**
     * Get the value of availablePlaces
     *
     * @return ?int
     */
    public function getAvailablePlaces(): ?int
    {
        return $this->availablePlaces;
    }

    /**
     * Set the value of availablePlaces
     *
     * @param ?int $availablePlaces
     *
     * @return self
     */
    public function setAvailablePlaces(?int $availablePlaces): self
    {
        $this->availablePlaces = $availablePlaces;

        return $this;
    }

    /**
     * Get the value of businessHours
     *
     * @return ?BusinessHours
     */
    public function getBusinessHours(): ?BusinessHours
    {
        return $this->businessHours;
    }

    /**
     * Set the value of businessHours
     *
     * @param ?BusinessHours $businessHours
     *
     * @return self
     */
    public function setBusinessHours(?BusinessHours $businessHours): self
    {
        $this->businessHours = $businessHours;

        return $this;
    }
}

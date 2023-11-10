<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ContactRepository;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Length(
        min: 3,
        minMessage: 'Votre nom doit contenir au minimum {{ limit }} characters',)]
    #[Assert\Regex(
        pattern:"/[<>.+\$%\/;:!?@€*-]/",
        match:false,
        message:"Votre nom ne peut pas contenir de caractères spéciaux")]
    #[ORM\Column(length: 255)]
    private ?string $full_name = null;

    #[Email(message: 'Le mail {{ value }} n\'est pas un e-mail valide.',)]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[Length(
        min: 10,
        minMessage: 'Votre sujet doit contenir au minimum {{ limit }} characters',)]
        #[Assert\Regex(
            pattern:"/[<>+\$%\/@€*-]/",
            match:false,
            message:"Votre sujet ne peut pas contenir de caractères spéciaux")]
    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[Length(
        min: 50,
        minMessage: 'Votre message doit contenir au minimum {{ limit }} characters',)]
        #[Assert\Regex(
            pattern:"/[<>+\$%\/@€*-]/",
            match:false,
            message:"Votre message ne peut pas contenir de caractères spéciaux")]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $is_read = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getcreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setcreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of phone
     *
     * @return ?string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @param ?string $phone
     *
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of is_read
     *
     * @return ?bool
     */
    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    /**
     * Set the value of is_read
     *
     * @param ?bool $is_read
     *
     * @return self
     */
    public function setIsRead(?bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }
}

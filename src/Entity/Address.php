<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: 'address')]
class Address
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Association::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(onDelete: 'CASCADE', nullable: true)]
    private ?Association $association = null;



    #[ORM\Column(nullable: true)]
    private ?bool $isUserFavorite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function isUserFavorite(): ?bool
    {
        return $this->isUserFavorite;
    }

    public function setIsUserFavorite(?bool $isUserFavorite): static
    {
        $this->isUserFavorite = $isUserFavorite;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;

        return $this;
    }

}

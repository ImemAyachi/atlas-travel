<?php

namespace App\Entity;

use App\Repository\PackageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PackageRepository::class)]
class Package
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $packageId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: 'text')]
    private ?string $destinations = null;

    #[ORM\Column]
    private ?int $availableSeats = null;

    #[ORM\Column(length: 255)]
    private ?string $packageImage = null;

    public function getPackageId(): ?int
    {
        return $this->packageId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDestinations(): ?string
    {
        return $this->destinations;
    }

    public function setDestinations(string $destinations): static
    {
        $this->destinations = $destinations;
        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->availableSeats;
    }

    public function setAvailableSeats(int $availableSeats): static
    {
        $this->availableSeats = $availableSeats;
        return $this;
    }

    public function getPackageImage(): ?string
    {
        return $this->packageImage;
    }

    public function setPackageImage(string $packageImage): static
    {
        $this->packageImage = $packageImage;
        return $this;
    }
} 
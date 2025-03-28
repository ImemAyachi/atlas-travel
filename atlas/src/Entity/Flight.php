<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightRepository::class)]
class Flight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $flightId = null;

    #[ORM\Column(length: 255)]
    private ?string $departure = null;

    #[ORM\Column(length: 255)]
    private ?string $destination = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $departureDate = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $returnDate = null;

    #[ORM\Column]
    private ?int $availableSeats = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne(targetEntity: Airline::class)]
    #[ORM\JoinColumn(name: 'airline_id', referencedColumnName: 'airline_id')]
    private ?Airline $airline = null;

    public function getFlightId(): ?int
    {
        return $this->flightId;
    }

    public function getDeparture(): ?string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): static
    {
        $this->departure = $departure;
        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): static
    {
        $this->destination = $destination;
        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeInterface $departureDate): static
    {
        $this->departureDate = $departureDate;
        return $this;
    }

    public function getReturnDate(): ?\DateTimeInterface
    {
        return $this->returnDate;
    }

    public function setReturnDate(\DateTimeInterface $returnDate): static
    {
        $this->returnDate = $returnDate;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getAirline(): ?Airline
    {
        return $this->airline;
    }

    public function setAirline(?Airline $airline): static
    {
        $this->airline = $airline;
        return $this;
    }
} 
<?php

namespace App\Entity;

use App\Repository\FlightReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlightReservationRepository::class)]
class FlightReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $reservationId = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Flight::class)]
    #[ORM\JoinColumn(name: 'flight_id', referencedColumnName: 'flight_id')]
    private ?Flight $flight = null;

    #[ORM\Column(length: 100)]
    private ?string $passengerName = null;

    #[ORM\Column(length: 100)]
    private ?string $passengerEmail = null;

    #[ORM\Column(length: 20)]
    private ?string $passengerPhone = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $reservationDate = null;

    #[ORM\Column]
    private ?int $numberOfPassengers = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $totalPrice = null;

    #[ORM\Column(length: 20)]
    private ?string $paymentStatus = null;

    #[ORM\Column(length: 20)]
    private ?string $reservationStatus = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $specialRequests = null;

    public function getReservationId(): ?int
    {
        return $this->reservationId;
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

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): static
    {
        $this->flight = $flight;
        return $this;
    }

    public function getPassengerName(): ?string
    {
        return $this->passengerName;
    }

    public function setPassengerName(string $passengerName): static
    {
        $this->passengerName = $passengerName;
        return $this;
    }

    public function getPassengerEmail(): ?string
    {
        return $this->passengerEmail;
    }

    public function setPassengerEmail(string $passengerEmail): static
    {
        $this->passengerEmail = $passengerEmail;
        return $this;
    }

    public function getPassengerPhone(): ?string
    {
        return $this->passengerPhone;
    }

    public function setPassengerPhone(string $passengerPhone): static
    {
        $this->passengerPhone = $passengerPhone;
        return $this;
    }

    public function getReservationDate(): ?\DateTimeInterface
    {
        return $this->reservationDate;
    }

    public function setReservationDate(\DateTimeInterface $reservationDate): static
    {
        $this->reservationDate = $reservationDate;
        return $this;
    }

    public function getNumberOfPassengers(): ?int
    {
        return $this->numberOfPassengers;
    }

    public function setNumberOfPassengers(int $numberOfPassengers): static
    {
        $this->numberOfPassengers = $numberOfPassengers;
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function getReservationStatus(): ?string
    {
        return $this->reservationStatus;
    }

    public function setReservationStatus(string $reservationStatus): static
    {
        $this->reservationStatus = $reservationStatus;
        return $this;
    }

    public function getSpecialRequests(): ?string
    {
        return $this->specialRequests;
    }

    public function setSpecialRequests(?string $specialRequests): static
    {
        $this->specialRequests = $specialRequests;
        return $this;
    }
} 
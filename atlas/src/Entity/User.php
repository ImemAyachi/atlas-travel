<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'utilisateur')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $surname = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 20)]
    private ?string $role = null;

    #[ORM\Column(name: "profileImage", length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column(name: "num_telph", length: 20, nullable: true)]
    private ?string $numTelph = null;

    #[ORM\Column(name: "voyageurPreferences", length: 255, nullable: true)]
    private ?string $voyageurPreferences = null;

    #[ORM\Column(name: "destinations_preferrees", type: 'text', nullable: true)]
    private ?string $destinationsPreferrees = null;

    #[ORM\Column(nullable: true)]
    private ?float $budget = null;

    #[ORM\Column(name: "reservationHistorique", type: 'text', nullable: true)]
    private ?string $reservationHistorique = null;

    #[ORM\Column(name: "completedTrips", type: 'text', nullable: true)]
    private ?string $completedTrips = null;

    #[ORM\Column(name: "assignedTickets", type: 'text', nullable: true)]
    private ?string $assignedTickets = null;

    #[ORM\Column(name: "adminPrivileges", type: 'text', nullable: true)]
    private ?string $adminPrivileges = null;

    private ?string $nom = null;

    private ?string $prenom = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    public function getNumTelph(): ?string
    {
        return $this->numTelph;
    }

    public function setNumTelph(?string $numTelph): static
    {
        $this->numTelph = $numTelph;
        return $this;
    }

    public function getVoyageurPreferences(): ?string
    {
        return $this->voyageurPreferences;
    }

    public function setVoyageurPreferences(?string $voyageurPreferences): static
    {
        $this->voyageurPreferences = $voyageurPreferences;
        return $this;
    }

    public function getDestinationsPreferrees(): ?string
    {
        return $this->destinationsPreferrees;
    }

    public function setDestinationsPreferrees(?string $destinationsPreferrees): static
    {
        $this->destinationsPreferrees = $destinationsPreferrees;
        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): static
    {
        $this->budget = $budget;
        return $this;
    }

    public function getReservationHistorique(): ?string
    {
        return $this->reservationHistorique;
    }

    public function setReservationHistorique(?string $reservationHistorique): static
    {
        $this->reservationHistorique = $reservationHistorique;
        return $this;
    }

    public function getCompletedTrips(): ?string
    {
        return $this->completedTrips;
    }

    public function setCompletedTrips(?string $completedTrips): static
    {
        $this->completedTrips = $completedTrips;
        return $this;
    }

    public function getAssignedTickets(): ?string
    {
        return $this->assignedTickets;
    }

    public function setAssignedTickets(?string $assignedTickets): static
    {
        $this->assignedTickets = $assignedTickets;
        return $this;
    }

    public function getAdminPrivileges(): ?string
    {
        return $this->adminPrivileges;
    }

    public function setAdminPrivileges(?string $adminPrivileges): static
    {
        $this->adminPrivileges = $adminPrivileges;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->name;
    }

    public function setNom(string $nom): self
    {
        $this->name = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->surname;
    }

    public function setPrenom(string $prenom): self
    {
        $this->surname = $prenom;
        return $this;
    }

    public function getRoles(): array
    {
        // If role is empty, default to 'tourist'
        $role = $this->role;
        if (empty($role)) {
            $role = 'tourist';
            // Optionally save this change to database
            // You can uncomment this if you want to fix all users when they log in
            // $this->role = $role;
        }
        return ['ROLE_' . strtoupper($role)];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
} 
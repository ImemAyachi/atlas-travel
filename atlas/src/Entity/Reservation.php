<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 20)]
    private ?string $typeChambre = null;

    #[ORM\Column]
    private ?int $nombrePersonnes = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateArrivee = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column]
    private ?int $nombreNuits = null;

    #[ORM\Column]
    private ?bool $petitDejeuner = null;

    #[ORM\Column]
    private ?bool $litSupplementaire = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vueSpecifique = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTypeChambre(): ?string
    {
        return $this->typeChambre;
    }

    public function setTypeChambre(string $typeChambre): static
    {
        $this->typeChambre = $typeChambre;
        return $this;
    }

    public function getNombrePersonnes(): ?int
    {
        return $this->nombrePersonnes;
    }

    public function setNombrePersonnes(int $nombrePersonnes): static
    {
        $this->nombrePersonnes = $nombrePersonnes;
        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): static
    {
        $this->dateArrivee = $dateArrivee;
        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;
        return $this;
    }

    public function getNombreNuits(): ?int
    {
        return $this->nombreNuits;
    }

    public function setNombreNuits(int $nombreNuits): static
    {
        $this->nombreNuits = $nombreNuits;
        return $this;
    }

    public function isPetitDejeuner(): ?bool
    {
        return $this->petitDejeuner;
    }

    public function setPetitDejeuner(bool $petitDejeuner): static
    {
        $this->petitDejeuner = $petitDejeuner;
        return $this;
    }

    public function isLitSupplementaire(): ?bool
    {
        return $this->litSupplementaire;
    }

    public function setLitSupplementaire(bool $litSupplementaire): static
    {
        $this->litSupplementaire = $litSupplementaire;
        return $this;
    }

    public function getVueSpecifique(): ?string
    {
        return $this->vueSpecifique;
    }

    public function setVueSpecifique(?string $vueSpecifique): static
    {
        $this->vueSpecifique = $vueSpecifique;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): static
    {
        $this->montantTotal = $montantTotal;
        return $this;
    }
} 
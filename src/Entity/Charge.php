<?php

namespace App\Entity;

use App\Repository\ChargeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChargeRepository::class)]
class Charge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Titre = null;

    #[ORM\Column]
    private ?float $Mantant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Fichier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCharge $Id_TypeCharge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getMantant(): ?float
    {
        return $this->Mantant;
    }

    public function setMantant(float $Mantant): self
    {
        $this->Mantant = $Mantant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->Fichier;
    }

    public function setFichier(?string $Fichier): self
    {
        $this->Fichier = $Fichier;

        return $this;
    }

    public function getIdTypeCharge(): ?TypeCharge
    {
        return $this->Id_TypeCharge;
    }

    public function setIdTypeCharge(?TypeCharge $Id_TypeCharge): self
    {
        $this->Id_TypeCharge = $Id_TypeCharge;

        return $this;
    }
}

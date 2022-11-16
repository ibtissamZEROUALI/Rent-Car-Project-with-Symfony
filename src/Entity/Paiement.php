<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Mantant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $PaiementDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $Id_Location = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaiementDate(): ?\DateTimeInterface
    {
        return $this->PaiementDate;
    }

    public function setPaiementDate(\DateTimeInterface $PaiementDate): self
    {
        $this->PaiementDate = $PaiementDate;

        return $this;
    }

    public function getIdLocation(): ?Location
    {
        return $this->Id_Location;
    }

    public function setIdLocation(?Location $Id_Location): self
    {
        $this->Id_Location = $Id_Location;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\PanneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanneRepository::class)]
class Panne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $TypePanne = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusPanne = null;

    #[ORM\Column(length: 255)]
    private ?string $Remarque = null;

    #[ORM\ManyToOne]
    private ?Vehicule $Id_Vehicule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypePanne(): ?string
    {
        return $this->TypePanne;
    }

    public function setTypePanne(string $TypePanne): self
    {
        $this->TypePanne = $TypePanne;

        return $this;
    }

    public function getStatusPanne(): ?string
    {
        return $this->StatusPanne;
    }

    public function setStatusPanne(string $StatusPanne): self
    {
        $this->StatusPanne = $StatusPanne;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->Remarque;
    }

    public function setRemarque(string $Remarque): self
    {
        $this->Remarque = $Remarque;

        return $this;
    }

    public function getIdVehicule(): ?Vehicule
    {
        return $this->Id_Vehicule;
    }

    public function setIdVehicule(?Vehicule $Id_Vehicule): self
    {
        $this->Id_Vehicule = $Id_Vehicule;

        return $this;
    }
}

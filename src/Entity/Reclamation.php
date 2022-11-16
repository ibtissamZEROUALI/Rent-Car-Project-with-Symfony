<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Raclamation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $Id_Location = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusReclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaclamation(): ?string
    {
        return $this->Raclamation;
    }

    public function setRaclamation(?string $Raclamation): self
    {
        $this->Raclamation = $Raclamation;

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

    public function getStatusReclamation(): ?string
    {
        return $this->StatusReclamation;
    }

    public function setStatusReclamation(string $StatusReclamation): self
    {
        $this->StatusReclamation = $StatusReclamation;

        return $this;
    }
}

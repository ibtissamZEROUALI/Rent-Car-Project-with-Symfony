<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueRepository::class)]
class Marque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Marque = null;

    #[ORM\Column(length: 255)]
    private ?string $LogoMarque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->Marque;
    }

    public function setMarque(string $Marque): self
    {
        $this->Marque = $Marque;

        return $this;
    }

    public function getLogoMarque(): ?string
    {
        return $this->LogoMarque;
    }

    public function setLogoMarque(string $LogoMarque): self
    {
        $this->LogoMarque = $LogoMarque;

        return $this;
    }
}

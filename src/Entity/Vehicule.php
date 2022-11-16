<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $PrixDay = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Image = null;

    #[ORM\Column]
    private ?int $ModelYear = null;

    #[ORM\Column]
    private ?int $Capacity = null;

    #[ORM\Column(length: 50)]
    private ?string $StatusVehicule = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateRegistration = null;

    #[ORM\ManyToOne]
    private ?TypeVehicule $Id_TypeVehicule = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Marque $Id_Marque = null;

    #[ORM\Column(length: 255)]
    private ?string $Model = null;

    #[ORM\Column(length: 255)]
    private ?string $Matricul = null;

   /* #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusVehicule $StatusVahicule = null;*/

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

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrixDay(): ?float
    {
        return $this->PrixDay;
    }

    public function setPrixDay(float $PrixDay): self
    {
        $this->PrixDay = $PrixDay;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getModelYear(): ?int
    {
        return $this->ModelYear;
    }

    public function setModelYear(int $ModelYear): self
    {
        $this->ModelYear = $ModelYear;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->Capacity;
    }

    public function setCapacity(int $Capacity): self
    {
        $this->Capacity = $Capacity;

        return $this;
    }

    public function getStatusVehicule(): ?string
    {
        return $this->StatusVehicule;
    }

    public function setStatusVehicule(string $StatusVehicule): self
    {
        $this->StatusVehicule = $StatusVehicule;

        return $this;
    }

    public function getDateRegistration(): ?\DateTimeInterface
    {
        return $this->DateRegistration;
    }

    public function setDateRegistration(\DateTimeInterface $DateRegistration): self
    {
        $this->DateRegistration = $DateRegistration;

        return $this;
    }

    public function getIdTypeVehicule(): ?TypeVehicule
    {
        return $this->Id_TypeVehicule;
    }

    public function setIdTypeVehicule(?TypeVehicule $Id_TypeVehicule): self
    {
        $this->Id_TypeVehicule = $Id_TypeVehicule;

        return $this;
    }

    public function getIdMarque(): ?Marque
    {
        return $this->Id_Marque;
    }

    public function setIdMarque(?Marque $Id_Marque): self
    {
        $this->Id_Marque = $Id_Marque;

        return $this;
    }

    /*public function getStatusVahicule(): ?StatusVehicule
    {
        return $this->StatusVahicule;
    }

    public function setStatusVahicule(?StatusVehicule $StatusVahicule): self
    {
        $this->StatusVahicule = $StatusVahicule;

        return $this;
    }*/

    public function getModel(): ?string
    {
        return $this->Model;
    }

    public function setModel(string $Model): self
    {
        $this->Model = $Model;

        return $this;
    }

    public function getMatricul(): ?string
    {
        return $this->Matricul;
    }

    public function setMatricul(string $Matricul): self
    {
        $this->Matricul = $Matricul;

        return $this;
    }
}

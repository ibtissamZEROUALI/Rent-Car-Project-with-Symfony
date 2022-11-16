<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $FromDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ToDate = null;

    #[ORM\Column]
    private ?float $Remise = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $Id_User = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicule $Id_Vehicule = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $Id_Client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateRegistration = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $Valide = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Note = null;

    /*#[ORM\ManyToOne]
    private ?Reservation $Id_Reservation = null;*/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->FromDate;
    }

    public function setFromDate(\DateTimeInterface $FromDate): self
    {
        $this->FromDate = $FromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->ToDate;
    }

    public function setToDate(\DateTimeInterface $ToDate): self
    {
        $this->ToDate = $ToDate;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->Remise;
    }

    public function setRemise(float $Remise): self
    {
        $this->Remise = $Remise;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->Id_User;
    }

    public function setIdUser(?User $Id_User): self
    {
        $this->Id_User = $Id_User;

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

    public function getIdClient(): ?Client
    {
        return $this->Id_Client;
    }

    public function setIdClient(?Client $Id_Client): self
    {
        $this->Id_Client = $Id_Client;

        return $this;
    }

   /* public function getIdReservation(): ?Reservation
    {
        return $this->Id_Reservation;
    }

    public function setIdReservation(?Reservation $Id_Reservation): self
    {
        $this->Id_Reservation = $Id_Reservation;

        return $this;
    }*/

   public function getDateRegistration(): ?\DateTimeInterface
   {
       return $this->DateRegistration;
   }

   public function setDateRegistration(?\DateTimeInterface $DateRegistration): self
   {
       $this->DateRegistration = $DateRegistration;

       return $this;
   }

   public function getValide(): ?int
   {
       return $this->Valide;
   }

   public function setValide(int $Valide): self
   {
       $this->Valide = $Valide;

       return $this;
   }

   public function getNote(): ?string
   {
       return $this->Note;
   }

   public function setNote(?string $Note): self
   {
       $this->Note = $Note;

       return $this;
   }

}

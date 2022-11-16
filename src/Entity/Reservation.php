<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $NumReservation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $FromDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ToDate = null;

    #[ORM\Column]
    private ?float $Remise = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusReservation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $Id_Client = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicule $Id_Vehicule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumReservation(): ?int
    {
        return $this->NumReservation;
    }

    public function setNumReservation(int $NumReservation): self
    {
        $this->NumReservation = $NumReservation;

        return $this;
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

    public function getStatusReservation(): ?string
    {
        return $this->StatusReservation;
    }

    public function setStatusReservation(string $StatusReservation): self
    {
        $this->StatusReservation = $StatusReservation;

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

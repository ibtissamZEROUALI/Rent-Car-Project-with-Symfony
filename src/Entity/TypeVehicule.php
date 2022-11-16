<?php


namespace App\Entity;

use App\Repository\TypeVehiculeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeVehiculeRepository::class)]
class TypeVehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeVehicule = null;

    #[ORM\Column(length: 255)]
    private ?string $ImageType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeVehicule(): ?string
    {
        return $this->TypeVehicule;
    }

    public function setTypeVehicule(string $TypeVehicule): self
    {
        $this->TypeVehicule = $TypeVehicule;

        return $this;
    }

    public function getImageType(): ?string
    {
        return $this->ImageType;
    }

    public function setImageType(string $ImageType): self
    {
        $this->ImageType = $ImageType;

        return $this;
    }
}

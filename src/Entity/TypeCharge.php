<?php

namespace App\Entity;

use App\Repository\TypeChargeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeChargeRepository::class)]
class TypeCharge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeCharge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeCharge(): ?string
    {
        return $this->TypeCharge;
    }

    public function setTypeCharge(string $TypeCharge): self
    {
        $this->TypeCharge = $TypeCharge;

        return $this;
    }
}

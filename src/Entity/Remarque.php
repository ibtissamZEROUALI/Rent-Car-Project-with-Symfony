<?php

namespace App\Entity;

use App\Repository\RemarqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RemarqueRepository::class)]
class Remarque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Remarque = null;

    #[ORM\ManyToOne(inversedBy: 'remarques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $IdClient = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdClient(): ?Client
    {
        return $this->IdClient;
    }

    public function setIdClient(?Client $IdClient): self
    {
        $this->IdClient = $IdClient;

        return $this;
    }
}

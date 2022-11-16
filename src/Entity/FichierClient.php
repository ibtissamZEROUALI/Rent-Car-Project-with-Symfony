<?php

namespace App\Entity;

use App\Repository\FichierClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierClientRepository::class)]
class FichierClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $FichierClient = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $Id_Client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichierClient(): ?string
    {
        return $this->FichierClient;
    }

    public function setFichierClient(?string $FichierClient): self
    {
        $this->FichierClient = $FichierClient;

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
}

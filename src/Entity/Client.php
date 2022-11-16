<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $Prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $Phone = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\Column(length: 50)]
    private ?string $Cin = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 50)]
    private ?string $Sexe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $Password = null;

    #[ORM\Column]
    private ?bool $Permition = null;

    #[ORM\OneToMany(mappedBy: 'IdClient', targetEntity: Remarque::class)]
    private Collection $remarques;

    public function __construct()
    {
        $this->remarques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->Phone;
    }

    public function setPhone(string $Phone): self
    {
        $this->Phone = $Phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->Cin;
    }

    public function setCin(string $Cin): self
    {
        $this->Cin = $Cin;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->Sexe;
    }

    public function setSexe(string $Sexe): self
    {
        $this->Sexe = $Sexe;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->DateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $DateNaissance): self
    {
        $this->DateNaissance = $DateNaissance;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    public function isPermition(): ?bool
    {
        return $this->Permition;
    }

    public function setPermition(int $Permition): self
    {
        $this->Permition = $Permition;

        return $this;
    }

    /**
     * @return Collection<int, Remarque>
     */
    public function getRemarques(): Collection
    {
        return $this->remarques;
    }

    public function addRemarque(Remarque $remarque): self
    {
        if (!$this->remarques->contains($remarque)) {
            $this->remarques->add($remarque);
            $remarque->setIdClient($this);
        }

        return $this;
    }

    public function removeRemarque(Remarque $remarque): self
    {
        if ($this->remarques->removeElement($remarque)) {
            // set the owning side to null (unless already changed)
            if ($remarque->getIdClient() === $this) {
                $remarque->setIdClient(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\RoleModuleRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: RoleModuleRepository::class)]
class RoleModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?RoleList $Id_RoleList = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Id_User = null;

    #[ORM\Column()]
    private ?int $Permition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdRoleList(): ?RoleList
    {
        return $this->Id_RoleList;
    }

    public function setIdRoleList(?RoleList $Id_RoleList): self
    {
        $this->Id_RoleList = $Id_RoleList;

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

    public function getPermition(): ?string
    {
        return $this->Permition;
    }

    public function setPermition(string $Permition): self
    {
        $this->Permition = $Permition;

        return $this;
    }
}

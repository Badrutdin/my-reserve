<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups('default')]
    private string $name;
    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\Column(type: "string", length: 10)]
    private string $type = "custom";


    #[ORM\Column(type: "boolean")]
    private bool $isEditable = true;

    #[ORM\Column(type: "boolean")]
    private bool $isDeletable = true;

    #[ORM\Column(type: 'json')]
    private array $permissions = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $validTypes = ['admin', 'default', 'custom'];
        if (!in_array($type, $validTypes, true)) {
            throw new \InvalidArgumentException("Invalid role type: $type");
        }
        $this->type = $type;
    }


    public function isEditable(): bool
    {
        return $this->isEditable;
    }

    public function setIsEditable(bool $isEditable): void
    {
        $this->isEditable = $isEditable;
    }

    public function isDeletable(): bool
    {
        return $this->isDeletable;
    }

    public function setIsDeletable(bool $isDeletable): void
    {
        $this->isDeletable = $isDeletable;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

}

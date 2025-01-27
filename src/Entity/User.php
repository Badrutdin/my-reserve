<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\Column(type: 'string', length: 255)]
    private string $roles = 'ROLE_USER';
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $apiToken = null;


    #[ORM\ManyToMany(targetEntity: Organization::class, inversedBy: 'users')]
    private Collection $organizations;

    #[ORM\OneToMany(targetEntity: OrganizationUsersRole::class, mappedBy: 'user')]
    private Collection $organizationUsersRoles;

    #[ORM\OneToMany(targetEntity: OrganizationUsersPosition::class, mappedBy: 'user')]
    private Collection $organizationUsersPositions;

    public function __construct()
    {
        $this->organizations = new ArrayCollection();
        $this->organizationUsersRoles = new ArrayCollection();
        $this->organizationUsersPositions = new ArrayCollection();
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->roles];
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Organization>
     */
    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(Organization $organization): static
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations->add($organization);
        }

        return $this;
    }

    public function removeOrganization(Organization $organization): static
    {
        $this->organizations->removeElement($organization);

        return $this;
    }


    public function getOrganizationUsersRoles(): Collection
    {
        return $this->organizationUsersRoles;
    }

    public function getRoleByOrganization(int $organizationId): ?Role
    {
        foreach ($this->organizationUsersRoles as $organizationUsersRole) {
            if ($organizationUsersRole->getOrganization()->getId() === $organizationId) {
                return $organizationUsersRole->getRole();
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPositionByOrganization(int $organizationId): ?Position
    {
        foreach ($this->organizationUsersPositions as $organizationUsersPosition) {
            if ($organizationUsersPosition->getOrganization()->getId() === $organizationId) {
                return $organizationUsersPosition->getPosition();
            }
        }

        return null;
    }

    public function eraseCredentials(): void
    {
        // Do nothing
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }
}

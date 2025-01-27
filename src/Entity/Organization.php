<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $subdomain;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\OneToMany(targetEntity: Role::class, mappedBy: 'organization')]
    private Collection $roles;

    /**
     * @var Collection<int, Position>
     */
    #[ORM\OneToMany(targetEntity: Position::class, mappedBy: 'organization')]
    private Collection $positions;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'organization')]
    private Collection $employees;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'organizations')]
    private Collection $users;
    #[ORM\OneToMany(targetEntity: OrganizationUsersRole::class, mappedBy: 'organization')]
    private Collection $organizationUsersRoles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->positions = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->organizationUsersRoles = new ArrayCollection();

    }

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
    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function setSubdomain(string $subdomain): self
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->setOrganization($this);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        if ($this->roles->removeElement($role)) {
            // set the owning side to null (unless already changed)
            if ($role->getOrganization() === $this) {
                $role->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Position>
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    public function addPosition(Position $position): static
    {
        if (!$this->positions->contains($position)) {
            $this->positions->add($position);
            $position->setOrganization($this);
        }

        return $this;
    }

    public function removePosition(Position $position): static
    {
        if ($this->positions->removeElement($position)) {
            // set the owning side to null (unless already changed)
            if ($position->getOrganization() === $this) {
                $position->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setOrganization($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getOrganization() === $this) {
                $employee->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addOrganization($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeOrganization($this);
        }

        return $this;
    }

    public function getOrganizationUsersRoles(): Collection
    {
        return $this->organizationUsersRoles;
    }
}

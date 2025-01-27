<?php

namespace App\Manager;

use App\Entity\Organization;
use App\Entity\OrganizationUsersPosition;
use App\Entity\OrganizationUsersRole;
use App\Entity\Position;
use App\Entity\Role;
use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\PositionRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserManager
{
    public function __construct(
        private UserRepository                   $userRepository,
        private PositionRepository               $positionRepository,
        private RoleRepository                   $roleRepository,
        private OrganizationUsersRoleManager     $organizationUsersRoleManager,
        private OrganizationUsersPositionManager $organizationUsersPositionManager,
        private EntityManagerInterface           $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function attachToOrganization(User $user, Organization $organization): void
    {
        if ($user->getOrganizations()->contains($organization)) ApiException::userAlreadyAssignException($user->getId());
        $user->addOrganization($organization);
    }

    public function detachFromOrganization(User $user, Organization $organization): void
    {
        if (!$user->getOrganizations()->contains($organization)) ApiException::userNotAssignException($user->getId());
        $user->removeOrganization($organization);
    }

    public function updateProperties(Organization $organization, User $user, array $data): void
    {
        if (isset($data['name'])) $user->setName($data['name']);

        if (isset($data['position'])) $this->setPosition($organization, $user, $data['position']);

        if (isset($data['role'])) $this->setRole($organization, $user, $data['role']);

    }

    /**TODO
     * set
     */
    private function setPosition(Organization $organization, User $user, int $dataPosition): void
    {
        $currentPositionId = $user->getPositionByOrganization($organization->getId())?->getId();
        if ($dataPosition != $currentPositionId) {
            $position = $this->positionRepository->findByOrganizationAndId($organization, $dataPosition);
            if (!$position) ApiException::positionNotFoundException($dataPosition);
            $this->assignPosition($user, $organization, $position);
        }
    }

    private function assignPosition(User $user, Organization $organization, Position $position): void
    {
        $hasAnotherPosition = $this->organizationUsersPositionManager->findPositionRelationByUserAndOrganization($user->getId(), $organization->getId());
        if ($hasAnotherPosition) {
            $hasAnotherPosition->setPosition($position);
        } else {
            $organizationUserPosition = new OrganizationUsersPosition();
            $organizationUserPosition->setUser($user);
            $organizationUserPosition->setOrganization($organization);

            $organizationUserPosition->setPosition($position);
            $this->entityManager->persist($organizationUserPosition);
        }
        $this->entityManager->flush();
    }

    private function setRole(Organization $organization, User $user, int $dataRole): void
    {
        $currentRoleId = $user->getRoleByOrganization($organization->getId())?->getId();
        if ($dataRole != $currentRoleId) {
            $role = $this->roleRepository->findByOrganizationAndId($organization, $dataRole);
            if (!$role) ApiException::roleNotFoundException($dataRole);
            $this->assignRole($user, $organization, $role);
        }
    }

    public function assignRole(User $user, Organization $organization, Role $role): void
    {
        $hasAnotherRole = $this->organizationUsersRoleManager->findRoleRelationByUserAndOrganization($user->getId(), $organization->getId());
        if ($hasAnotherRole) {
            $hasAnotherRole->setRole($role);
        } else {
            $organizationUserRole = new OrganizationUsersRole();
            $organizationUserRole->setUser($user);
            $organizationUserRole->setOrganization($organization);
            $organizationUserRole->setRole($role);
            $this->entityManager->persist($organizationUserRole);
        }
        $this->entityManager->flush();
    }

    public function findByOrganization(int $organizationId, int $userId): User
    {

        $user = $this->userRepository->findByOrganizationAndId($organizationId, $userId);
        if (!$user) ApiException::userNotFoundException($userId);
        return $user;
    }

    public function setProperties(Organization $organization, User $user, array $data): void
    {
        $user->setName($data['name']);
        if (isset($data['position'])) $this->setPosition($organization, $user, $data['position']);
        $user->setEmail($data['email']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

    }


}

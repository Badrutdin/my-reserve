<?php

namespace App\Service;

use App\DataTransformer\PaginationTransformer;
use App\DataTransformer\RoleTransformer;
use App\Entity\Organization;
use App\Entity\Role;
use App\Exception\ApiException;
use App\Manager\RoleManager;
use App\Repository\RoleRepository;
use App\Validator\RoleValidationService;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(
        private RoleRepository         $roleRepository,
        private RoleTransformer        $roleTransformer,
        private PaginationTransformer  $paginationTransformer,
        private EntityManagerInterface $entityManager,
        private RoleValidationService  $roleValidationService,
        private RoleManager            $roleManager
    )
    {
    }

    public function getByOrganization(Organization $organization, int $page, int $limit): array
    {
        $roles = $this->roleRepository->findByOrganization($organization);
        $total = $this->roleRepository->listCount($organization);
        return [
            'pagination' => $this->paginationTransformer->transform($page, $limit, $total, ceil($total / $limit)),
            'items' => array_map(fn($role) => $this->roleTransformer->transform($role), $roles),
        ];
    }

    public function getByOrganizationAndId(Organization $organization, $roleId): array
    {
        $role = $this->roleManager->findByOrganizationAndId($organization, $roleId);
        return [$this->roleTransformer->transform($role)];
    }

    public function create(Organization $organization, array $data): array
    {

        $this->roleValidationService->validateOnCreate($data);
        $role = $this->roleRepository->findOneBy(['name' => $data['name'], 'organization' => $organization]);
        if ($role) ApiException::roleAlreadyExistsException($role->getId());
        $role = new Role();
        $this->roleManager->setProperties($organization, $role, $data);
        $this->save($role);
        return [$this->roleTransformer->transform($role)];
    }

    public function save(Role $role): void
    {
        if (!$this->entityManager->contains($role)) $this->entityManager->persist($role);
        $this->entityManager->flush();
    }

    public function update(Organization $organization, int $roleId, array $data): array
    {
        $this->roleValidationService->validateOnUpdate($data);
        $role = $this->roleManager->findByOrganizationAndId($organization, $roleId);
        $this->roleManager->updateProperties($organization, $role, $data);
        $this->save($role);
        return [$this->roleTransformer->transform($role)];
    }

    public function delete(Organization $organization, int $roleId): array
    {
        $role = $this->roleManager->findByOrganizationAndId($organization, $roleId);
        $associatedUsers = $this->roleManager->findUsersByRoleAndOrganization($roleId, $organization->getId());
        if ($associatedUsers) ApiException::roleUsedException($roleId, $associatedUsers);
        $this->entityManager->remove($role);
        $this->save($role);
        return ['message' => "role $roleId is deleted"];
    }
}
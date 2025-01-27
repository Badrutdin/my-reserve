<?php

namespace App\Manager;

use App\Entity\Organization;
use App\Entity\Role;
use App\Exception\ApiException;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Security\Permission;
use App\Security\PermissionHierarchy;

class RoleManager
{
    public function __construct(private RoleRepository $roleRepository, private UserRepository $userRepository)
    {
    }

//    public function addPermissionWithDependencies(Role $role, string $permission): void
//    {
//        $dependencies = PermissionHierarchy::getDependencies($permission);
//        $permissions = array_unique(array_merge($role->getPermissions(), $dependencies, [$permission]));
//        $role->setPermissions($permissions);
//    }

    public function findByOrganizationAndId(Organization $organization, int $roleId): Role
    {
        $role = $this->roleRepository->findByOrganizationAndId($organization, $roleId);
        if (!$role) ApiException::roleNotFoundException($roleId);
        return $role;
    }


    public function findUsersByRoleAndOrganization($roleId, $organizationId): array
    {
        return $this->userRepository->findByRoleAndOrganization($roleId, $organizationId);
    }

    public function setProperties(Organization $organization, Role $role, array $data): void
    {
        $role->setName($data['name']);
        $role->setOrganization($organization);
        $role->setIsDeletable(true);
        $role->setIsEditable(true);
        $role->setType('custom');
    }

    public function updateProperties(Organization $organization, Role $role, array $data): void
    {
        if (!$role->isEditable()) ApiException::roleCantEditExistsException($role->getId());
        if (isset($data['name'])) $role->setName($data['name']);
        if (isset($data['permissions'])) $role->setPermissions($data['permissions']);
    }

    private function validatePermissions(array $permissions): void
    {
        // Получаем список допустимых прав из централизованного пула
        $validPermissions = Permission::cases();  // Для Enum

        foreach ($permissions as $permission) {
            if (!in_array($permission, $validPermissions, true)) {
                throw new \InvalidArgumentException(sprintf('Недопустимое право: %s', $permission));
            }
        }
    }

}
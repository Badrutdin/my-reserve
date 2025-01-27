<?php

namespace App\Manager;

use App\Entity\OrganizationUsersRole;
use App\Entity\Role;
use App\Repository\OrganizationUsersRoleRepository;

class OrganizationUsersRoleManager
{
    public function __construct(private OrganizationUsersRoleRepository $repository)
    {
    }



    public function findRoleRelationByUserAndOrganization(int $userId, int $organizationId): ?OrganizationUsersRole
    {
        return $this->repository->findByOrganizationAndUser($organizationId, $userId);

    }
}
<?php

namespace App\Manager;

use App\Entity\OrganizationUsersPosition;
use App\Repository\OrganizationUsersPositionRepository;

class OrganizationUsersPositionManager
{
    public function __construct(private OrganizationUsersPositionRepository $repository)
    {
    }



    public function findPositionRelationByUserAndOrganization(int $userId, int $organizationId): ?OrganizationUsersPosition
    {
        return $this->repository->findByOrganizationAndUser($organizationId, $userId);

    }
}
<?php

namespace App\Manager;

use App\Entity\Organization;
use App\Entity\Position;
use App\Exception\ApiException;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;


class PositionManager
{
    public function __construct(
        private PositionRepository $positionRepository,
        private UserRepository     $userRepository
    )
    {
    }


    public function findByOrganization(Organization $organization, int $positionId): Position
    {
        $position = $this->positionRepository->findByOrganizationAndId($organization, $positionId);
        if (!$position) ApiException::positionNotFoundException($positionId);
        return $position;
    }


    public function findUsersByOrganizationAndId($positionId, $organizationId): array
    {
        return $this->userRepository->findByPositionAndOrganization($positionId, $organizationId);
    }

    public function findEmployees(Position $position): array
    {
        return $position->getEmployees()->map(fn($employee) => $employee->getId())->toArray();
    }


    public function setProperties(Organization $organization, Position $position, array $data): void
    {
        $position->setName($data['name']);
        $position->setOrganization($organization);
    }

    public function updateProperties(Organization $organization, Position $position, array $data): void
    {
        if (isset($data['name'])) $position->setName($data['name']);
    }

}
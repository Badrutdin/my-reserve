<?php

namespace App\Service;

use App\DataTransformer\PaginationTransformer;
use App\DataTransformer\PositionTransformer;
use App\Entity\Organization;
use App\Entity\Position;
use App\Exception\ApiException;
use App\Manager\PositionManager;
use App\Repository\PositionRepository;
use App\Validator\PositionValidationService;
use Doctrine\ORM\EntityManagerInterface;

class PositionService
{
    public function __construct(
        private PositionRepository        $positionRepository,
        private PositionTransformer       $positionTransformer,
        private PaginationTransformer     $paginationTransformer,
        private EntityManagerInterface    $entityManager,
        private PositionValidationService $positionValidationService,
        private PositionManager           $positionManager
    )
    {
    }

    public function getByOrganization(Organization $organization, int $page, int $limit): array
    {
        $positions = $this->positionRepository->findByOrganization($organization);
        $total = $this->positionRepository->listCount($organization);
        return [
            'pagination' => $this->paginationTransformer->transform($page, $limit, $total, ceil($total / $limit)),
            'items' => array_map(fn($position) => $this->positionTransformer->transform($position), $positions),
        ];
    }

    public function getById(Organization $organization, $positionId): array
    {
        $position = $this->positionManager->findByOrganization($organization, $positionId);
        return [$this->positionTransformer->transform($position)];
    }

    public function create(Organization $organization, array $data): array
    {

        $this->positionValidationService->validateOnCreate($data);
        $position = $this->positionRepository->findOneBy(['name' => $data['name'], 'organization' => $organization]);
        if ($position) ApiException::positionAlreadyExistsException($position->getId());
        $position = new Position();
        $this->positionManager->setProperties($organization, $position, $data);
        $this->save($position);
        return [$this->positionTransformer->transform($position)];
    }

    public function save(Position $position): void
    {
        if (!$this->entityManager->contains($position)) $this->entityManager->persist($position);
        $this->entityManager->flush();
    }

    public function update(Organization $organization, int $positionId, array $data): array
    {
        $this->positionValidationService->validateOnUpdate($data);
        $position = $this->positionManager->findByOrganization($organization, $positionId);
        $this->positionManager->updateProperties($organization, $position, $data);
        $this->save($position);
        return [$this->positionTransformer->transform($position)];
    }

    public function delete(Organization $organization, int $positionId): array
    {
        $position = $this->positionManager->findByOrganization($organization, $positionId);
        $associatedUsers = $this->positionManager->findUsersByOrganizationAndId($positionId, $organization->getId());
        if ($associatedUsers) ApiException::positionUsedUsersException($positionId, $associatedUsers);
        $associatedEmployees = $this->positionManager->findEmployees($position);
        if ($associatedEmployees) ApiException::positionUsedEmployeesException($positionId, $associatedEmployees);
        $this->entityManager->remove($position);
        $this->entityManager->flush();
        return ['message' => "position $positionId is deleted"];
    }
}
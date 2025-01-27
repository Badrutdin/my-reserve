<?php

namespace App\Service;

use App\DataTransformer\PaginationTransformer;
use App\DataTransformer\UserTransformer;
use App\Entity\Organization;
use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Validator\UserValidationService;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private UserRepository         $userRepository,
        private UserTransformer        $userTransformer,
        private PaginationTransformer  $paginationTransformer,
        private EntityManagerInterface $entityManager,
        private UserValidationService  $userValidationService,
        private UserManager            $userManager,
    )
    {
    }

    public function getByOrganization(Organization $organization, int $page, int $limit): array
    {
        $users = $this->userRepository->findByOrganization($organization->getId(), $page, $limit);
        $total = $this->userRepository->listCount($organization->getId());
        return [
            'pagination' => $this->paginationTransformer->transform($page, $limit, $total, ceil($total / $limit)),
            'items' => array_map(fn($user) => $this->userTransformer->transform($user, $organization->getId()), $users),
        ];
    }

    public function getByOrganizationAndId(int $organizationId, int $userId): array
    {
        $user = $this->userManager->findByOrganization($organizationId, $userId);
        return [$this->userTransformer->transform($user, $organizationId)];
    }


    public function create(Organization $organization, array $data): array
    {

        $this->userValidationService->validateOnCreate($data);
        $user = $this->userRepository->findOneBy(['email' => $data['email']]);
        if (!$user) {
            $user = new User();
            $this->userManager->setProperties($organization, $user, $data);
        }
        $this->userManager->attachToOrganization($user, $organization);
        $this->save($user);
        return [$this->userTransformer->transform($user, $organization->getId())];
    }

    private function save(User $user): void
    {
        if (!$this->entityManager->contains($user)) $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(Organization $organization, int $userId, array $data): array
    {
        $this->userValidationService->validateOnUpdate($data);
        $user = $this->userManager->findByOrganization($organization->getId(), $userId);
        $this->userManager->updateProperties($organization, $user, $data);
        $this->save($user);
        return [$this->userTransformer->transform($user, $organization->getId())];
    }

    public function delete(Organization $organization, int $userId): array
    {
        $user = $this->userManager->findByOrganization($organization->getId(), $userId);
        $this->userManager->detachFromOrganization($user, $organization);
        if ($user->getOrganizations()->isEmpty()) $this->entityManager->remove($user);
        $this->save($user);
        return ['message' => "user $userId is deleted"];
    }
}

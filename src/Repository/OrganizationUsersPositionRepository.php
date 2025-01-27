<?php

namespace App\Repository;

use App\Entity\OrganizationUsersPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationUsersPosition>
 */
class OrganizationUsersPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationUsersPosition::class);
    }

    public function findByOrganizationAndUser(int $organizationId, int $userId): ?OrganizationUsersPosition
    {
        return $this->createQueryBuilder('our')
            ->andWhere('our.organization = :organizationId')
            ->andWhere('our.user = :userId')
            ->setParameter('organizationId', $organizationId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserAndOrganization(int $userId, int $organizationId): ?OrganizationUsersPosition
    {
        return $this->createQueryBuilder('our')
            ->andWhere('our.user = :userId')
            ->andWhere('our.organization = :organizationId')
            ->setParameter('userId', $userId)
            ->setParameter('organizationId', $organizationId)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

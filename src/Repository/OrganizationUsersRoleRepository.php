<?php

namespace App\Repository;

use App\Entity\OrganizationUsersRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrganizationUsersRole>
 */
class OrganizationUsersRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrganizationUsersRole::class);
    }

    public function findByOrganizationAndUser(int $organizationId, int $userId): ?OrganizationUsersRole
    {
        return $this->createQueryBuilder('our')
            ->andWhere('our.organization = :organizationId')
            ->andWhere('our.user = :userId')
            ->setParameter('organizationId', $organizationId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserAndOrganization(int $userId, int $organizationId): ?OrganizationUsersRole
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

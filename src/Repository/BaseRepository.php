<?php

namespace App\Repository;


use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function findByOrganization(Organization $organization,int $page =1 ,int $limit =50)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.organization = :organization')
            ->setParameter('organization', $organization)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findByOrganizationAndId(Organization $organization, int $entityId): ?object
    {
        return $this->createQueryBuilder('e') // 'e' — алиас для сущности.
        ->andWhere('e.organization = :organization') // Условие по организации.
        ->andWhere('e.id = :entityId')               // Условие по ID.
        ->setParameter('organization', $organization)
            ->setParameter('entityId', $entityId)
            ->getQuery()
            ->getOneOrNullResult();                     // Возвращает одну запись или null.
    }

    public function listCount(Organization $organization): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.organization = :organization')
            ->setParameter('organization', $organization);

        return (int)$qb->getQuery()->getSingleScalarResult();
    }


}
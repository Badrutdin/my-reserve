<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByOrganization(int $organizationId, int $page = 1, int $limit = 50)
    {

        return $this->createQueryBuilder('d')
            ->innerJoin('d.organizations', 'o') // Связь с таблицей организаций
            ->andWhere('o.id = :organizationId')    // Фильтрация по конкретной организации
            ->setParameter('organizationId', $organizationId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    public function findByOrganizationAndId(int $organizationId, int $userId)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.organizations', 'o') // Связь с таблицей организаций
            ->andWhere('o.id = :organizationId') // Фильтрация по ID организации
            ->andWhere('d.id = :userId')         // Условие по ID пользователя
            ->setParameter('organizationId', $organizationId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();             // Возвращает одну запись или null, если запись не найдена
    }


    public function listCount(int $organizationId): int
    {
        $qb = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->innerJoin('d.organizations', 'o') // Связь с таблицей организаций
            ->andWhere('o = :organizationId')    // Фильтрация по конкретной организации
            ->setParameter('organizationId', $organizationId);
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function findByRoleAndOrganization($roleId, $organizationId)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.id,u.name')
            ->join('u.organizationUsersRoles', 'our')
            ->where('our.role = :roleId')
            ->andWhere('our.organization = :organizationId')
            ->setParameter('roleId', $roleId)
            ->setParameter('organizationId', $organizationId)
            ->getQuery();
        return $query->getResult();
    }

    public function findByPositionAndOrganization($positionId, $organizationId)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.id, u.name')
            ->join('u.organizationUsersPositions', 'oup') // предполагаем, что связь называется organizationUsersPositions
            ->where('oup.position = :positionId')
            ->andWhere('oup.organization = :organizationId')
            ->setParameter('positionId', $positionId)
            ->setParameter('organizationId', $organizationId)
            ->getQuery();

        return $query->getResult();
    }

}

<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function getTeamsByIds(array $ids): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.name', \Doctrine\Common\Collections\Criteria::ASC)
            ->andWhere('t.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function getRandTeams($count): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('RAND()', \Doctrine\Common\Collections\Criteria::ASC)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }
    
    public function findOneByTeamName($value): ?Team
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

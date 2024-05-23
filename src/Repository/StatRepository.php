<?php

namespace App\Repository;

use App\Entity\Stat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stat>
 */
class StatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stat::class);
    }
    
    public function getAvgGoals(): string
    {
        return $this->createQueryBuilder('s')
                ->select('avg(s.goal)/avg(s.gamesQuantity)')
                ->getQuery()
                ->getSingleScalarResult()
        ;
    }
}

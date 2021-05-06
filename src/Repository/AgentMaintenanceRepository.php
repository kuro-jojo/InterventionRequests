<?php

namespace App\Repository;

use App\Entity\AgentMaintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AgentMaintenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method AgentMaintenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method AgentMaintenance[]    findAll()
 * @method AgentMaintenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentMaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgentMaintenance::class);
    }

    // /**
    //  * @return AgentMaintenance[] Returns an array of AgentMaintenance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AgentMaintenance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

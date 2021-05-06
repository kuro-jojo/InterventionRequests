<?php

namespace App\Repository;

use App\Entity\DemandeIntervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeIntervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeIntervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeIntervention[]    findAll()
 * @method DemandeIntervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeInterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeIntervention::class);
    }

    // /**
    //  * @return DemandeIntervention[] Returns an array of DemandeIntervention objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DemandeIntervention
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

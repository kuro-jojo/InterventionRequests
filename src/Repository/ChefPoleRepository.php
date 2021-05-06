<?php

namespace App\Repository;

use App\Entity\ChefPole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChefPole|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChefPole|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChefPole[]    findAll()
 * @method ChefPole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChefPoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChefPole::class);
    }

    // /**
    //  * @return ChefPole[] Returns an array of ChefPole objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChefPole
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Entity\DemandeIntervention;
use App\Entity\SearchAsk;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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


    public function findAskBySearch(SearchAsk $searchAsk,int $agent = null)
    {
        $qb = $this->createQueryBuilder('d');
        if ($searchAsk->getTypeDefaillance()) {
            $qb->andWhere('d.poleConcerne = :typeDefaillance')
                ->setParameter('typeDefaillance', $searchAsk->getTypeDefaillance());
        }

        if ($searchAsk->getStatutDemande()) {
            $qb->andWhere('d.statut = :statutDemande')
                ->setParameter('statutDemande', $searchAsk->getStatutDemande());
        }

        if ($searchAsk->getPrioriteDemande()) {
            $qb->andWhere('d.priorite = :prioriteDemande')
                ->setParameter('prioriteDemande', $searchAsk->getPrioriteDemande());
        }
        if ($agent) {
            $qb->andWhere(':agent MEMBER OF d.traiteursDemande')
            ->setParameter('agent',$agent);
        }
        return $qb->getQuery();
    }

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

    //trouver toutes les demandes d'intervention

    /**
     * @return Demande
     */
    public function findAllAskQuery(): array
    {
        return $this->findAskQuery()
            ->getQuery()
            ->getResult();
    }

    public function getNumberOfAsk(): int
    {

        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNumberOfAskByPole(int $pole_id): int
    {

        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->andWhere('a.poleConcerne = :val')
            ->setParameter('val', $pole_id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNumberOfAskByStatus($status): int
    {

        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->andWhere('a.statut = :val')
            ->setParameter('val', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNumberOfAskByStatusByPole($status,int $pole_id): int
    {

        return $this->createQueryBuilder('a')
            ->select('count(a)')
            ->andWhere('a.statut = :val')
            ->andWhere('a.poleConcerne = :id')
            ->setParameter('id', $pole_id)
            ->setParameter('val', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

 
}

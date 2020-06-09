<?php

namespace App\Repository;

use App\Entity\EstateStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstateStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstateStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstateStatus[]    findAll()
 * @method EstateStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstateStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstateStatus::class);
    }

    // /**
    //  * @return EstateStatus[] Returns an array of EstateStatus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EstateStatus
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

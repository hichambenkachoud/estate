<?php

namespace App\Repository;

use App\Entity\EstateType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstateType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstateType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstateType[]    findAll()
 * @method EstateType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstateTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstateType::class);
    }

    // /**
    //  * @return EstateType[] Returns an array of EstateType objects
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
    public function findOneBySomeField($value): ?EstateType
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

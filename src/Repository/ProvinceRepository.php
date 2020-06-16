<?php

namespace App\Repository;

use App\Entity\Province;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Province|null find($id, $lockMode = null, $lockVersion = null)
 * @method Province|null findOneBy(array $criteria, array $orderBy = null)
 * @method Province[]    findAll()
 * @method Province[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProvinceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Province::class);
    }

    // /**
    //  * @return City[] Returns an array of Province objects
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

    /**
     * @param string $province
     * @return Province[] Returns an array of Provinces objects
     */
    public function findByProvinceName($province)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('lower(p.name) like :name')
            ->setParameter('name', strtolower($province).'%')
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findBySomeIds()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.code IN (:val)')
            ->setParameter('val', ['casablanca', 'rabat', 'marrakech'])
            ->getQuery()
            ->getResult()
            ;
    }
}

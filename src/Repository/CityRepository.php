<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    /**
     * @param string $city
     * @return City[] Returns an array of City objects
     */
    public function findByCityName($city)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.province', 'province')
            ->andWhere('lower(c.name) like :name OR lower(province.name) like :name')
            ->setParameter('name', strtolower($city).'%')
            ->orderBy('c.id', 'ASC')
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

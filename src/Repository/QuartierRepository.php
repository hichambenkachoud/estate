<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\Quartier;
use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quartier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quartier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quartier[]    findAll()
 * @method Quartier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuartierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quartier::class);
    }

    // /**
    //  * @return Quartier[] Returns an array of Quartier objects
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
     * @param string $quartier
     * @return Quartier[] Returns an array of Quartiers objects
     */
    public function findByQuartierName($quartier)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('lower(q.name) like :name')
            ->setParameter('name', strtolower($quartier).'%')
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
            ;
    }
}

<?php

namespace App\Repository;

use App\Entity\Agency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    // /**
    //  * @return Agency[] Returns an array of Agency objects
    //  */

    public function findAllAgency($page = 1, $city = null, $name = null, $maxPerPage = Agency::NUM_ITEMS)
    {
        $q =  $this->createQueryBuilder('a')
            ->leftJoin('a.city', 'city');

        //var_dump($city);die();
        if ($city != '' && !empty($city) && $city != null){
            $q->andWhere('city.code like :cityP')->setParameter('cityP', "%".$city."%");
        }
        if ($name != '' && !empty($name) && $name != null){
            $q->andWhere('lower(a.name) like :nameP')->setParameter('nameP', "%".strtolower($name)."%");
        }

        $q->andWhere('a.enabled = :enabled')
            ->setParameter('enabled', true)
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);
        /*->getQuery()
        ->getResult();*/

        return new Paginator($q);
    }



    public function loadAgencyByEmail($email): ?Agency
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}

<?php

namespace App\Repository;

use App\Entity\Members;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Members|null find($id, $lockMode = null, $lockVersion = null)
 * @method Members|null findOneBy(array $criteria, array $orderBy = null)
 * @method Members[]    findAll()
 * @method Members[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Members::class);
    }


    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadMemberByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('(u.email = :email OR u.userName = :email) AND u.enabled = :enabled')
            ->setParameter('email', $username)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /***
     * @param \Symfony\Bridge\Doctrine\Security\User\string $token
     * @return mixed|\Symfony\Component\Security\Core\User\UserInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadMemberByToken($token)
    {
        return $this->createQueryBuilder('u')
            ->where('u.resetToken = :resetToken OR u.confirmationToken = :resetToken')
            ->setParameter('resetToken', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /***
     *
     * @return mixed|\Symfony\Component\Security\Core\User\UserInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getAgents()
    {
        return $this->createQueryBuilder('u')
            ->join('u.adverts', 'a')
            ->where('u.enabled = :enabled')
            ->andWhere('a.valid = :enabled')
            ->setParameter('enabled', 1)
            ->groupBy('u.id')
            ->having("count(a.id) > :limit")
            ->setParameter('limit', 0)
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}

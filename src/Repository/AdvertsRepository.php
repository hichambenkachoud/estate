<?php

namespace App\Repository;

use App\Entity\Adverts;
use App\Entity\AdvertType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Adverts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adverts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adverts[]    findAll()
 * @method Adverts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adverts::class);
    }

    // /**
    //  * @return Adverts[] Returns an array of Adverts objects
    //  */

    public function findAdvertsByCity($city, $page, $maxPerPage = Adverts::NUM_ITEMS)
    {
        $q =  $this->createQueryBuilder('a')
            ->andWhere('a.city = :city')
            ->andWhere('(a.valid = :valid and a.refused = :refused)')
            ->setParameter('city', $city)
            ->setParameter('valid', true)
            ->setParameter('refused', false)
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);
            /*->getQuery()
            ->getResult();*/

            return new Paginator($q);
    }
    public function findAllValid($page, $maxPerPage = Adverts::NUM_ITEMS)
    {
        $q =  $this->createQueryBuilder('a')
            ->andWhere('(a.valid = :valid and a.refused = :refused)')
            ->setParameter('valid', true)
            ->setParameter('refused', false)
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);

            return new Paginator($q);
    }
    public function findOneSpecial()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('(a.valid = :valid and a.refused = :refused) and a.price > :price')
            ->setParameter('valid', true)
            ->setParameter('refused', false)
            ->setParameter('price', 100000)
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
    public function findAllValidHome($advertType, $estateType, $city, $page, $maxPerPage = Adverts::NUM_ITEMS)
    {
        $q =  $this->createQueryBuilder('a')
            ->join('a.advertType', 'advertType')
            ->join('a.estateType', 'estateType')
            ->leftJoin('a.city', 'city')
            ->leftJoin('a.quartier', 'quartier')
            ->andWhere('advertType.id = :advertTypeId')
            ->setParameter('advertTypeId', $advertType)
            ->andWhere('estateType.id = :estateTypeId')
            ->setParameter('estateTypeId', $estateType);
        if ($city != '' && !empty($city) && $city != null){
            $q->andWhere('lower(city.code) like :city OR lower(quartier.code) like :city')
                ->setParameter('city', '%'.strtolower($city).'%');
        }

        $q->andWhere('(a.valid = :valid and a.refused = :refused)')
            ->setParameter('valid', true)
            ->setParameter('refused', false)
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);

            return new Paginator($q);
    }
    public function findAllByCity($advertType, $estateType, $city, $page, $maxPerPage = Adverts::NUM_ITEMS)
    {
        $q =  $this->createQueryBuilder('a')
            ->join('a.advertType', 'advertType')
            ->join('a.estateType', 'estateType')
            ->leftJoin('a.city', 'city')
            ->andWhere('advertType.id = :advertTypeId')
            ->setParameter('advertTypeId', $advertType)
            ->andWhere('estateType.id = :estateTypeId')
            ->setParameter('estateTypeId', $estateType);
        if ($city != '' && !empty($city) && $city != null){
            $q->andWhere('lower(city.code) like :city ')
                ->setParameter('city', '%'.strtolower($city).'%');
        }

        $q->andWhere('(a.valid = :valid and a.refused = :refused)')
            ->setParameter('valid', true)
            ->setParameter('refused', false)
            ->orderBy('a.createDate', 'DESC')
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);

            return new Paginator($q);
    }

    public function findAllSellByCriteria($estateType, $priceMin,
                                      $priceMax, $areaMin, $areaMax, $publication, $advertStatus, $rooms = array(), $bathrooms = [], $characters = [], $floors = array(), $sorted = '',
                                      $page = 1, $maxPerPage = Adverts::NUM_ITEMS){

        $currentDate = date('Y-m-d H:i:s');

        $query = $this->createQueryBuilder('a');
        $query->join('a.advertType', 'advertType')
            ->join('a.estateType', 'estateType')
            ->leftJoin('a.estateStatus', 'estateStatus');
        $query->where('(a.valid = :valid AND a.refused = :refused)');
        $query->andWhere('lower(advertType.code) = :advertTypeCode')->setParameter('advertTypeCode', strtolower(AdvertType::ADVERT_TYPE_SELL));
        if ($estateType != '' && !empty($estateType)){
            $query->andWhere('estateType.id = :estateTypeId')->setParameter('estateTypeId', intval($estateType));
        }

        if ($priceMin != 0 && $priceMin != NULL){
            $query->andwhere('(a.price >= :minPrice)')->setParameter('minPrice', $priceMin);
        }
        if ($priceMax != 0 && $priceMax != NULL){
            $query->andwhere('(a.price <= :maxPrice)')->setParameter('maxPrice', $priceMax);
        }
        if ($areaMin != 0 && $areaMin != NULL){
            $query->andwhere('(a.area >= :minArea)')->setParameter('minArea', $areaMin);
        }
        if ($areaMax != 0 && $areaMax != NULL){
            $query->andwhere('(a.area <= :maxArea)')->setParameter('maxArea', $areaMax);
        }
        if (count($rooms) > 0 && is_array($rooms)){
            //var_dump($rooms);die();
            if (!in_array(5, $rooms)){
                $query->andwhere('(a.rooms IN (:bathrooms))')->setParameter('bathrooms', $rooms);
            }else{
                $query->andwhere('(a.rooms IN (:bathrooms)) OR a.rooms >= :minBathrooms')->setParameter('rooms', $rooms)->setParameter('minBathrooms', 5);
            }
        }
        if (count($bathrooms) > 0 && is_array($bathrooms)){
            //var_dump($rooms);die();
            if (!in_array(5, $bathrooms)){
                $query->andwhere('(a.rooms IN (:rooms))')->setParameter('rooms', $bathrooms);
            }else{
                $query->andwhere('(a.rooms IN (:rooms)) OR a.rooms >= :minRooms')->setParameter('rooms', $bathrooms)->setParameter('minRooms', 5);
            }
        }
        if (count($floors) > 0 && is_array($floors)){
            if (in_array(5, $floors)){
                $query->andwhere('(a.floor IN (:floors)) OR (a.floor >= :minFloors AND a.floor <= :maxFloors)')
                    ->setParameter('floors', $floors)->setParameter('minFloors', 1)->setParameter('maxFloors', 9);
            }elseif (in_array(10, $floors)){
                $query->andwhere('(a.floor IN (:floors)) OR a.floor >= :minFloors')->setParameter('floors', $rooms)->setParameter('minFloors', 10);
            }else{
                $query->andwhere('(a.floor IN (:floors)) ')->setParameter('floors', $floors);
            }
        }
        if (count($advertStatus) > 0 && is_array($advertStatus)){
            $query->andWhere('estateStatus.id IN (:status)')->setParameter('status', $advertStatus);
        }
        if (count($characters) > 0 && is_array($characters)){
            foreach ($characters as $key => $char){
                if ($key ==0){
                    $query->andWhere('a.characteristics like :chars'.$char)->setParameter('chars'.$char, '%"'.$char.'"%');
                }else{
                    $query->orWhere('a.characteristics like :chars'.$char)->setParameter('chars'.$char, '%"'.$char.'"%');
                }

            }
        }

        if ($publication != '0' && $publication != null){
            //$startDate = date('Y-m-d H:i:s', strtotime($currentDate.' - '.$publication. ' day'));
            $startDate = new \DateTime('-'.$publication.' day');
            $startDate = $startDate->format('Y-m-d H:i:s');
            $query->andWhere('a.createDate BETWEEN :startDate AND :endDate ')->setParameter('startDate', $startDate)->setParameter('endDate', $currentDate);
        }
        if ($sorted != '' && !empty($sorted)){
            $sortOptions = explode('-', $sorted);
            if (is_array($sortOptions) && count($sortOptions) == 2 && in_array($sortOptions[0], ['price', 'area', 'createDate'])  && in_array($sortOptions[1], ['desc', 'asc']) ){
                $query->orderBy('a.'.trim($sortOptions[0]), trim(strtoupper($sortOptions[1])));
            }
        }else{
            $query->orderBy('a.createDate', 'DESC');
        }

        $query->setParameter('valid', true)->setParameter('refused', false)
        ->setFirstResult(($page - 1) * $maxPerPage)
        ->setMaxResults($maxPerPage);

        return new Paginator($query);

    }

    public function findAllRentByCriteria($rentType, $estateType, $priceMin,
                                          $priceMax, $areaMin, $areaMax, $publication, $advertStatus, $rooms = [], $floors = [],$bathrooms = [], $characters = [], $sorted = '',
                                          $page=1, $maxPerPage = Adverts::NUM_ITEMS){

        $currentDate = date('Y-m-d H:i:s');
        $query = $this->createQueryBuilder('a');
        $query->join('a.advertType', 'advertType')
            ->join('a.estateType', 'estateType')
            ->leftJoin('a.estateStatus', 'estateStatus');
        $query->where('(a.valid = :valid AND a.refused = :refused)');
        $query->andWhere('lower(advertType.code) = :advertTypeCode')->setParameter('advertTypeCode', strtolower(AdvertType::ADVERT_TYPE_RENT));
        if ($estateType != '' && !empty($estateType)){
            $query->andWhere('estateType.id = :estateTypeId')->setParameter('estateTypeId', intval($estateType));
        }
        if ($rentType != '' && !empty($rentType)){
            $query->andWhere('a.rentType = :rentType')->setParameter('rentType', strtolower($rentType));
        }

        if ($priceMin != 0 && $priceMin != NULL){
            $query->andwhere('(a.price >= :minPrice)')->setParameter('minPrice', $priceMin);
        }
        if ($priceMax != 0 && $priceMax != NULL){
            $query->andwhere('(a.price <= :maxPrice)')->setParameter('maxPrice', $priceMax);
        }
        if ($areaMin != 0 && $areaMin != NULL){
            $query->andwhere('(a.area >= :minArea)')->setParameter('minArea', $areaMin);
        }
        if ($areaMax != 0 && $areaMax != NULL){
            $query->andwhere('(a.area <= :maxArea)')->setParameter('maxArea', $areaMax);
        }
        if (count($rooms) > 0 && is_array($rooms)){
            //var_dump($rooms);die();
            if (!in_array(5, $rooms)){
                $query->andwhere('(a.rooms IN (:rooms))')->setParameter('rooms', $rooms);
            }else{
                $query->andwhere('(a.rooms IN (:rooms)) OR a.rooms >= :minRooms')->setParameter('rooms', $rooms)->setParameter('minRooms', 5);
            }
        }
        if (count($bathrooms) > 0 && is_array($bathrooms)){
            //var_dump($rooms);die();
            if (!in_array(5, $bathrooms)){
                $query->andwhere('(a.rooms IN (:rooms))')->setParameter('rooms', $bathrooms);
            }else{
                $query->andwhere('(a.rooms IN (:rooms)) OR a.rooms >= :minRooms')->setParameter('rooms', $bathrooms)->setParameter('minRooms', 5);
            }
        }
        if (count($floors) > 0 && is_array($floors)){
            if (in_array(5, $floors)){
                $query->andwhere('(a.floor IN (:floors)) OR (a.floor >= :minFloors AND a.floor <= :maxFloors)')
                    ->setParameter('floors', $floors)->setParameter('minFloors', 1)->setParameter('maxFloors', 9);
            }elseif (in_array(10, $floors)){
                $query->andwhere('(a.floor IN (:floors)) OR a.floor >= :minFloors')->setParameter('floors', $rooms)->setParameter('minFloors', 10);
            }else{
                $query->andwhere('(a.floor IN (:floors)) ')->setParameter('floors', $floors);
            }
        }
        if (count($advertStatus) > 0 && is_array($advertStatus)){
            $query->andWhere('estateStatus.id IN (:status)')->setParameter('status', $advertStatus);
        }
        if (count($characters) > 0 && is_array($characters)){
            foreach ($characters as $key => $char){
                if ($key ==0){
                    $query->andWhere('a.characteristics like :chars'.$char)->setParameter('chars'.$char, '%"'.$char.'"%');
                }else{
                    $query->orWhere('a.characteristics like :chars'.$char)->setParameter('chars'.$char, '%"'.$char.'"%');
                }

            }
        }
        if ($publication != '0' && $publication != null){
            //$startDate = date('Y-m-d H:i:s', strtotime($currentDate.' - '.$publication. ' day'));
            $startDate = new \DateTime('-'.$publication.' day');
            $startDate = $startDate->format('Y-m-d H:i:s');
            $query->andWhere('a.createDate BETWEEN :startDate AND :endDate ')->setParameter('startDate', $startDate)->setParameter('endDate', $currentDate);
        }
        if ($sorted != '' && !empty($sorted)){
            $sortOptions = explode('-', $sorted);
            if (is_array($sortOptions) && count($sortOptions) == 2 && in_array($sortOptions[0], ['price', 'area', 'createDate'])  && in_array($sortOptions[1], ['desc', 'asc']) ){
                $query->orderBy('a.'.trim($sortOptions[0]), trim(strtoupper($sortOptions[1])));
            }
        }else{
            $query->orderBy('a.createDate', 'DESC');
        }

        $query->setParameter('valid', true)->setParameter('refused', false)
            ->setFirstResult(($page - 1) * $maxPerPage)
            ->setMaxResults($maxPerPage);

        return new Paginator($query);
    }


    /*
    public function findOneBySomeField($value): ?Adverts
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

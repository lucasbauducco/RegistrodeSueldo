<?php

namespace App\Repository;

use App\Entity\UserBusqueda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserBusqueda|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBusqueda|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBusqueda[]    findAll()
 * @method UserBusqueda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBusquedaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBusqueda::class);
    }

    // /**
    //  * @return UserBusqueda[] Returns an array of UserBusqueda objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserBusqueda
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

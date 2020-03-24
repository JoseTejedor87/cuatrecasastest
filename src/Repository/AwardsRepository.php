<?php

namespace App\Repository;

use App\Entity\Awards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Awards|null find($id, $lockMode = null, $lockVersion = null)
 * @method Awards|null findOneBy(array $criteria, array $orderBy = null)
 * @method Awards[]    findAll()
 * @method Awards[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwardsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Awards::class);
    }

    // /**
    //  * @return Awards[] Returns an array of Awards objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Awards
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

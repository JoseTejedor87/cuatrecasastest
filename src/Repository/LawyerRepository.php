<?php

namespace App\Repository;

use App\Entity\Lawyer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lawyer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lawyer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lawyer[]    findAll()
 * @method Lawyer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LawyerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lawyer::class);
    }

    // /**
    //  * @return Lawyer[] Returns an array of Lawyer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lawyer
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

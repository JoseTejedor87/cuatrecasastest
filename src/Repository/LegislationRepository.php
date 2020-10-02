<?php

namespace App\Repository;

use App\Entity\Legislation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Legislation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Legislation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Legislation[]    findAll()
 * @method Legislation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LegislationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Legislation::class);
    }

    // /**
    //  * @return Legislation[] Returns an array of Legislation objects
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
    public function findOneBySomeField($value): ?Legislation
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

<?php

namespace App\Repository;

use App\Entity\ActivityTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ActivityTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityTranslation[]    findAll()
 * @method ActivityTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityTranslation::class);
    }

    // /**
    //  * @return ActivityTranslation[] Returns an array of ActivityTranslation objects
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
    public function findOneBySomeField($value): ?ActivityTranslation
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

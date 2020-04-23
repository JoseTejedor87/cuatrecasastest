<?php

namespace App\Repository;

use App\Entity\AwardTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AwardTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AwardTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AwardTranslation[]    findAll()
 * @method AwardTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwardTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwardTranslation::class);
    }

    // /**
    //  * @return AwardTranslation[] Returns an array of AwardTranslation objects
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
    public function findOneBySomeField($value): ?AwardTranslation
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

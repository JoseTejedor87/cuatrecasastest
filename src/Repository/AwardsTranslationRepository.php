<?php

namespace App\Repository;

use App\Entity\AwardsTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AwardsTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AwardsTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AwardsTranslation[]    findAll()
 * @method AwardsTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwardsTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwardsTranslation::class);
    }

    // /**
    //  * @return AwardsTranslation[] Returns an array of AwardsTranslation objects
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
    public function findOneBySomeField($value): ?AwardsTranslation
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

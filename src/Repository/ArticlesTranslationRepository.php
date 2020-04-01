<?php

namespace App\Repository;

use App\Entity\ArticlesTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ArticlesTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticlesTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticlesTranslation[]    findAll()
 * @method ArticlesTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticlesTranslation::class);
    }

    // /**
    //  * @return ArticlesTranslation[] Returns an array of ArticlesTranslation objects
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
    public function findOneBySomeField($value): ?ArticlesTranslation
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

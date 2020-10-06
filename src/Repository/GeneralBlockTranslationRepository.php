<?php

namespace App\Repository;

use App\Entity\GeneralBlockTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeneralBlockTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneralBlockTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneralBlockTranslation[]    findAll()
 * @method GeneralBlockTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneralBlockTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneralBlockTranslation::class);
    }

    // /**
    //  * @return GeneralBlockTranslation[] Returns an array of GeneralBlockTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GeneralBlockTranslation
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

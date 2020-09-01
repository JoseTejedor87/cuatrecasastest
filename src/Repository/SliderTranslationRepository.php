<?php

namespace App\Repository;

use App\Entity\SliderTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SliderTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SliderTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SliderTranslation[]    findAll()
 * @method SliderTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SliderTranslation::class);
    }

    // /**
    //  * @return SliderTranslation[] Returns an array of SliderTranslation objects
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
    public function findOneBySomeField($value): ?SliderTranslation
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

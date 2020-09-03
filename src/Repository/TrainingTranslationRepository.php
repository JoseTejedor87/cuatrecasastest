<?php

namespace App\Repository;

use App\Entity\TrainingTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TrainingTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingTranslation[]    findAll()
 * @method TrainingTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingTranslation::class);
    }

    // /**
    //  * @return TrainingTranslation[] Returns an array of TrainingTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TrainingTranslation
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

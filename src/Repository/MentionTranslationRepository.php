<?php

namespace App\Repository;

use App\Entity\MentionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MentionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MentionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MentionTranslation[]    findAll()
 * @method MentionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MentionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MentionTranslation::class);
    }

    // /**
    //  * @return MentionTranslation[] Returns an array of MentionTranslation objects
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
    public function findOneBySomeField($value): ?MentionTranslation
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

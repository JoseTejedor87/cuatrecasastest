<?php

namespace App\Repository;

use App\Entity\QuestionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method QuestionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionTranslation[]    findAll()
 * @method QuestionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionTranslation::class);
    }

    // /**
    //  * @return QuestionTranslation[] Returns an array of QuestionTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProgramTranslation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

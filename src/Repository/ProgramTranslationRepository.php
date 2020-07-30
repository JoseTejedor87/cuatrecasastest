<?php

namespace App\Repository;

use App\Entity\ProgramTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProgramTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgramTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgramTranslation[]    findAll()
 * @method ProgramTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramTranslation::class);
    }

    // /**
    //  * @return ProgramTranslation[] Returns an array of ProgramTranslation objects
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

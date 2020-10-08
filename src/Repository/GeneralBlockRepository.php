<?php

namespace App\Repository;

use App\Entity\GeneralBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GeneralBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method GeneralBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method GeneralBlock[]    findAll()
 * @method GeneralBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GeneralBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GeneralBlock::class);
    }

    // /**
    //  * @return GeneralBlock[] Returns an array of GeneralBlock objects
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
    public function findOneBySomeField($value): ?GeneralBlock
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

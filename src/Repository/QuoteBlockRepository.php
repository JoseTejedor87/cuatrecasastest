<?php

namespace App\Repository;

use App\Entity\QuoteBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method QuoteBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuoteBlock[]    findAll()
 * @method QuoteBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteBlock::class);
    }

    // /**
    //  * @return QuoteBlock[] Returns an array of QuoteBlock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuoteBlock
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\PublicationBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublicationBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationBlock[]    findAll()
 * @method PublicationBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationBlock::class);
    }

    // /**
    //  * @return PublicationBlock[] Returns an array of PublicationBlock objects
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
    public function findOneBySomeField($value): ?PublicationBlock
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

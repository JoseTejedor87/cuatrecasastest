<?php

namespace App\Repository;

use App\Entity\EventsBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventsBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventsBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventsBlock[]    findAll()
 * @method EventsBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsBlockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventsBlock::class);
    }

    // /**
    //  * @return EventsBlock[] Returns an array of EventsBlock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventsBlock
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

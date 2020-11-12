<?php

namespace App\Repository;

use App\Entity\PublicationLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PublicationLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationLink[]    findAll()
 * @method PublicationLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationLink::class);
    }

    // /**
    //  * @return PublicationLink[] Returns an array of PublicationLink objects
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
    public function findOneBySomeField($value): ?PublicationLink
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

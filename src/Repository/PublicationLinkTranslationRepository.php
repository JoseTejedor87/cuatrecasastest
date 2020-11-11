<?php

namespace App\Repository;

use App\Entity\PublicationLinkTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PublicationLinkTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationLinkTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationLinkTranslation[]    findAll()
 * @method PublicationLinkTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationLinkTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationLinkTranslation::class);
    }

    // /**
    //  * @return PublicationLinkTranslation[] Returns an array of PublicationLinkTranslation objects
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
    public function findOneBySomeField($value): ?PublicationLinkTranslation
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

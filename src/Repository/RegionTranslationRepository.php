<?php


namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\RegionTranslation;

/**
 * @method RegionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegionTranslation[]    findAll()
 * @method RegionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class RegionTranslationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegionTranslation::class);
    }

}
<?php

namespace App\Repository;

use App\Entity\Slider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;
/**
 * @method Slider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slider[]    findAll()
 * @method Slider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Slider::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        // not use
        return null;
    }
    
    public function getAllByPriorityRegion($bannerID)
    {
        $query = $this->createPublishedQueryBuilder('s');
        $this->priorBuilderClause($query, 's.offices');
        return $query->join('s.banners', 'b')
            ->andWhere('b.id = :banner_id')
            ->setParameter('banner_id', $bannerID)
            ->orderBy('s.priority','ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllByPriority($bannerID)
    {
        return $query = $this->createPublishedQueryBuilder('s')
            ->join('s.banners', 'b')
            ->andWhere('b.id = :banner_id')
            ->setParameter('banner_id', $bannerID)
            ->orderBy('s.priority','ASC')
            ->getQuery()
            ->getResult();
        
    }
    
    // /**
    //  * @return Slider[] Returns an array of Slider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Slider
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

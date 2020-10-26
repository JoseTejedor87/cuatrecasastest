<?php

namespace App\Repository;

use App\Entity\Home;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Home|null find($id, $lockMode = null, $lockVersion = null)
 * @method Home|null findOneBy(array $criteria, array $orderBy = null)
 * @method Home[]    findAll()
 * @method Home[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
// class HomeRepository extends ServiceEntityRepository
class HomeRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    /*
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Home::class);
    }
    */

    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Home::class);
    }    

    public function getInstanceByRequest(Request $request)
    {

        if ($id = $request->attributes->get('id')) {
            return $this->createPublishedQueryBuilder('h')
                    ->andWhere('h.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                   ->getOneOrNullResult();
        }
        return null;
    }

    // /**
    //  * @return Home[] Returns an array of Home objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Home
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

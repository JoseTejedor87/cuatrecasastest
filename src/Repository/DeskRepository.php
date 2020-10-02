<?php

namespace App\Repository;

use App\Entity\Desk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Desk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Desk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Desk[]    findAll()
 * @method Desk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeskRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Desk::class);
    }

    public function getInstanceByRequest(Request $request)
    {
        if ($slug = $request->attributes->get('slug')) {
            return $this->createQueryBuilder('p')
                ->join('p.translations', 't')
                ->where('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return null;
    }

    public function getDeskByName(Request $request){
        $entityManager = $this->getEntityManager();
        
        $region = $request->attributes->get('_region');
        $language = $request->attributes->get('_locale');

        $query = $entityManager->createQuery(
                "SELECT d FROM App\Entity\Desk d INNER JOIN d.translations t 
                WHERE d.regions LIKE :region AND d.languages LIKE :language AND t.locale LIKE :local
                AND d.highlighted = true ORDER BY t.title ASC "
        )->setParameters(array( 
                'language' => '%'.$language.'%',
                'region' => '%'.$region.'%',
                'local' => '%'.$language.'%',
        ));

        return $query;
    }

    // /**
    //  * @return Desk[] Returns an array of Desk objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Desk
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

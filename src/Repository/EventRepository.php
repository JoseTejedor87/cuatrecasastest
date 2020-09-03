<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    public function __construct(ManagerRegistry $registry, NavigationService $navigation)
    {
        parent::__construct($registry, $navigation, Event::class);
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

    public function findByActivities($activities)
    {
        $activitiesA = array();
        
        if($activities){
            foreach ($activities as $key => $activity) {
                array_push($activitiesA,$activity->getId());
             }
        }
        $results =  $this->createPublishedQueryBuilder('p');
            if ($activitiesA) {
                $results =  $results->innerJoin('p.activities', 'a')
                ->andWhere('a.id in (:activity)')
                ->setParameter('activity',$activitiesA);
                
            }
            //$results =  $results->andWhere("p.startDate>CURRENT_TIMESTAMP()");
            $results =  $results->orderBy('p.startDate', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
             
   
        return $results;

    }
    public function findFilteredBy($arrayFields){

        // se quitan los filtros que necesitan un join de tablas 
        if ( isset ( $arrayFields['title']) && $arrayFields['title'] != ''){
            $title = $arrayFields['title'];
            unset($arrayFields['title']);
        }

        if ( isset ( $arrayFields['finDesde']) && isset ( $arrayFields['finDesde'])){
            $finDesde = $arrayFields['finDesde'];
            unset($arrayFields['finDesde']);
        }

        if( isset ( $arrayFields['finHasta'])  && isset ( $arrayFields['finHasta'])){
            $finHasta = $arrayFields['finHasta'];
            unset($arrayFields['finHasta']);
        }

        if( isset ( $arrayFields['inicioDesde'])  && isset ( $arrayFields['inicioDesde'])){
            $inicioDesde = $arrayFields['inicioDesde'];
            unset($arrayFields['inicioDesde']);
        }

        if( isset ( $arrayFields['inicioHasta'])  && isset ( $arrayFields['inicioHasta'])){
            $inicioHasta = $arrayFields['inicioHasta'];
            unset($arrayFields['inicioHasta']);
        }



        $query = $this->filterByFieldsQueryBuilder($arrayFields,'e');

        if ( isset ( $title)){
            $query->join('e.translations', 't')
                ->andWhere('t.title LIKE :titulo')
                ->setParameter('titulo', '%'.$title.'%');
        }
        if (isset($finDesde)){
            $query->andWhere('e.endDate > :desde')
                ->setParameter('desde', $finDesde->format('Y-m-d'));
        }

        if( isset ( $finHasta )){
            $query->andWhere('e.endDate < :hasta')                
                ->setParameter('hasta', $finHasta->format('Y-m-d'));
        }

        if( isset ( $inicioDesde )){
            $query->andWhere('e.startDate > :desde')                
                ->setParameter('desde', $inicioDesde->format('Y-m-d'));
        }

        if( isset ( $inicioHasta )){
            $query->andWhere('e.startDate < :hasta')                
                ->setParameter('hasta', $inicioHasta->format('Y-m-d'));
        }

        return  $query->getQuery()->getResult();

    }
    // /**
    //  * @return Event[] Returns an array of Event objects
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
    public function findOneBySomeField($value): ?Event
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

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
        $MAXRESULT = 5;
        $activitiesA = array();
        if ($activities) {
            foreach ($activities as $key => $activity) {
                array_push($activitiesA, $activity->getId());
            }
        }
        $query =  $this->createPublishedQueryBuilder('p');

        if ($activitiesA) {
            $query->innerJoin('p.activities', 'a')
            ->andWhere('a.id in (:activity)')
            ->setParameter('activity', $activitiesA);
        }
        $query->orderBy('p.startDate', 'DESC')->setMaxResults($MAXRESULT);

        $queryPrior = clone $query;
        //$queryPrior->join('p.lawyers', 'l');
        $this->priorBuilderClause($queryPrior, 'p.office');
        $eventsPrior = $queryPrior->getQuery()->getResult();

        $totalEvents = [];
        foreach ($eventsPrior as $key => $item) {
            $totalEvents[$item->getId()] = $item;
        }

        $eventsAll = $query->getQuery()->getResult();
        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($eventsAll as $key => $item) {
            if (!isset($totalEvents[$item->getId()])) {
                array_push($totalEvents, $item);
            }
        }

        return array_slice($totalEvents, 0, $MAXRESULT);
    }

    public function findFilteredBy($arrayFields)
    {

        // se quitan los filtros que necesitan un join de tablas
        if (isset($arrayFields['title']) && $arrayFields['title'] != '') {
            $title = $arrayFields['title'];
            unset($arrayFields['title']);
        }

        if (isset($arrayFields['finDesde']) && isset($arrayFields['finDesde'])) {
            $finDesde = $arrayFields['finDesde'];
            unset($arrayFields['finDesde']);
        }

        if (isset($arrayFields['finHasta'])  && isset($arrayFields['finHasta'])) {
            $finHasta = $arrayFields['finHasta'];
            unset($arrayFields['finHasta']);
        }

        if (isset($arrayFields['inicioDesde'])  && isset($arrayFields['inicioDesde'])) {
            $inicioDesde = $arrayFields['inicioDesde'];
            unset($arrayFields['inicioDesde']);
        }

        if (isset($arrayFields['inicioHasta'])  && isset($arrayFields['inicioHasta'])) {
            $inicioHasta = $arrayFields['inicioHasta'];
            unset($arrayFields['inicioHasta']);
        }



        $query = $this->filterByFieldsQueryBuilder($arrayFields, 'e');

        if (isset($title)) {
            $query->join('e.translations', 't')
                ->andWhere('t.title LIKE :titulo')
                ->setParameter('titulo', '%'.$title.'%');
        }
        if (isset($finDesde)) {
            $query->andWhere('e.endDate > :desde')
                ->setParameter('desde', $finDesde->format('Y-m-d'));
        }

        if (isset($finHasta)) {
            $query->andWhere('e.endDate < :hasta')
                ->setParameter('hasta', $finHasta->format('Y-m-d'));
        }

        if (isset($inicioDesde)) {
            $query->andWhere('e.startDate > :desde')
                ->setParameter('desde', $inicioDesde->format('Y-m-d'));
        }

        if (isset($inicioHasta)) {
            $query->andWhere('e.startDate < :hasta')
                ->setParameter('hasta', $inicioHasta->format('Y-m-d'));
        }

        return  $query->getQuery()->getResult();
    }

    public function findFeaturedByActivities($activities)
    {
        $MAXRESULT = 5;
        $activitiesA = array();
        if ($activities) {
            foreach ($activities as $key => $activity) {
                array_push($activitiesA, $activity->getId());
            }
        }
        $query =  $this->createPublishedQueryBuilder('p');

        if ($activitiesA) {
            $query->innerJoin('p.activities', 'a')
            ->andWhere('a.id in (:activity)')
            ->setParameter('activity', $activitiesA);
        }
        $query->andWhere('p.featured = 1');
        $query->orderBy('p.startDate', 'DESC')->setMaxResults($MAXRESULT);

        $queryPrior = clone $query;
        //$queryPrior->join('p.lawyers', 'l');
        $this->priorBuilderClause($queryPrior, 'p.office');
        $eventsPrior = $queryPrior->getQuery()->getResult();

        $totalEvents = [];
        foreach ($eventsPrior as $key => $item) {
            $totalEvents[$item->getId()] = $item;
        }

        $eventsAll = $query->getQuery()->getResult();
        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($eventsAll as $key => $item) {
            if (!isset($totalEvents[$item->getId()])) {
                array_push($totalEvents, $item);
            }
        }

        return array_slice($totalEvents, 0, $MAXRESULT);
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

<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ManagerRegistry;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use App\Controller\Web\NavigationService;
use App\Repository\PublishableEntityRepository;
use App\Repository\PublishableInterfaceRepository;

/**
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends PublishableEntityRepository implements PublishableInterfaceRepository
{
    protected $imagineCacheManager;

    public function __construct(ManagerRegistry $registry, NavigationService $navigation, CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
        parent::__construct($registry, $navigation, Publication::class);
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

    public function findByActivities($activities, $maxResult= 10)
    {
        $activitiesA = array();
        if ($activities) {
            foreach ($activities as $key => $activity) {
                array_push($activitiesA, $activity->getId());
            }
        }

        $returnPublications = [];
        $results =  $this->createPublishedQueryBuilder('p');
        if ($activitiesA) {
            $results =  $results->innerJoin('p.activities', 'a')
            ->andWhere('a.id in (:activity)')
            ->setParameter('activity', $activitiesA);
        }

        // ZONE DE PRIORIZACION
        $place = $this->getNavigation()->getParams()->get('app.office_place')[$this->getNavigation()->getRegion()];
        $results->join('p.offices', 'o')
            ->andWhere('o.place = :place')
            ->setParameter('place', $place);
        //---------------------

        $results = $this->orderByDaySentences($results, 'p', '100')
            ->setMaxResults($maxResult)
            ->getQuery()
            ->getResult();

        foreach ($results as $key => $item) {
            $returnPublications[$item->getId()] = $item;
        }

        ///// consulta por todas las regiones
        $results =  $this->createPublishedQueryBuilder('p');
        if ($activitiesA) {
            $results =  $results->innerJoin('p.activities', 'a')
                ->andWhere('a.id in (:activity)')
                ->setParameter('activity', $activitiesA);
        }

        $results =  $results->orderBy('p.publication_date', 'DESC')
                ->setMaxResults($maxResult)
                ->getQuery()
                ->getResult();

        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($results as $key => $item) {
            if (!isset($returnPublications[$item->getId()])) {
                array_push($returnPublications, $item);
            }
        }

        return $this->setTypePublication(array_slice($returnPublications, 0, $maxResult));
    }

    public function findByActivities_($activities)  // DEPRECAR por el priorizado
    {
        $activitiesA = array();
        if ($activities) {
            foreach ($activities as $key => $activity) {
                array_push($activitiesA, $activity->getId());
            }
        }
        $results =  $this->createPublishedQueryBuilder('p');
        if ($activitiesA) {
            $results =  $results->innerJoin('p.activities', 'a')
                ->andWhere('a.id in (:activity)')
                ->setParameter('activity', $activitiesA);
        }
        //$results =  $results->andWhere("p.startDate>CURRENT_TIMESTAMP()");
        $results =  $results->orderBy('p.publication_date', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

        return $this->setTypePublication($results);
    }

    // agrega el tipo de publicacion
    public function setTypePublication($publications)
    {
        foreach ($publications as $key => $value) {
            $value->fechaPubli = $value->getPublicationDate()->format("j F Y");
            if ($value instanceof \App\Entity\LegalNovelty || $value instanceof \App\Entity\Academy) {
                $value->type = 'academy';
            }
            if ($value instanceof \App\Entity\Opinion) {
                $value->type = 'opinion';
            }
            if ($value instanceof \App\Entity\News) {
                $value->type = 'news';
            }
            $value->photo = $this->getPhotoPathByFilter($value, 'lawyers_grid');
            if (!$value->photo) {
                $value->photo = 'https://via.placeholder.com/800x400';
            }
        }

        return $publications;
    }

    protected function getPhotoPathByFilter($publication, $filter)
    {
        if ($photos = $publication->getAttachments()) {
            foreach ($photos as $key => $photo) {
                if ($photo->getType() == "publication_main_photo") {
                    $photo = $this->imagineCacheManager->getBrowserPath(
                        '/resources/' . $photo->getFileName(),
                        $filter
                    );
                    return $photo;
                }
            }
        }
    }

    public function findFilteredBy($arrayFields)
    {

        // se quitan los filtros que necesitan un join de tablas
        if (isset($arrayFields['title']) && $arrayFields['title'] != '') {
            $title = $arrayFields['title'];
            unset($arrayFields['title']);
        }

        /*if (isset($arrayFields['finDesde']) && isset($arrayFields['finDesde'])) {
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
        }*/



        $query = $this->filterByFieldsQueryBuilder($arrayFields, 'p');

        if (isset($title)) {
            $query->join('p.translations', 't')
                ->andWhere('t.title LIKE :titulo')
                ->setParameter('titulo', '%'.$title.'%');
        }
        /*  if (isset($finDesde)) {
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
          }*/
        /* foreach ($query->getQuery()->getResult() as $r) {
             var_dump($r->translate('es')->getTitle());
         }
         die();*/
        return  $query->getQuery()->getResult();
    }
    // /**
    //  * @return Publication[] Returns an array of Publication objects
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
    public function findOneBySomeField($value): ?Article
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

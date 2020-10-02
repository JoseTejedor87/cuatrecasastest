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

    public function __construct(ManagerRegistry $registry, NavigationService $navigation,CacheManager $imagineCacheManager)
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
                ->setParameter('activity',  $activitiesA);
            }
            //$results =  $results->andWhere("p.startDate>CURRENT_TIMESTAMP()");
            $results =  $results->orderBy('p.publication_date', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();
        foreach ($results as $key => $value) {
                $value->fechaPubli = $value->getPublicationDate()->format("j F Y");
            if ($value instanceof \App\Entity\LegalNovelty || $value instanceof \App\Entity\Academy ){
                $value->type = 'academy';
            }
            if ($value instanceof \App\Entity\Opinion){
                $value->type = 'opinion';
            }
            if ($value instanceof \App\Entity\News){
                $value->type = 'news';
            }
            $value->photo = $this->getPhotoPathByFilter($value, 'lawyers_grid');
            if(!$value->photo){
                $value->photo = 'https://via.placeholder.com/800x400';
            }
                    
        }
        return $results;
    }
    protected function getPhotoPathByFilter($publication, $filter)
    {
        if ($photos = $publication->getAttachments()) {
            foreach ($photos as $key => $photo) {

                if($photo->getType() == "publication_main_photo"){
                    $photo = $this->imagineCacheManager->getBrowserPath(
                        '/resources/' . $photo->getFileName(),
                        $filter
                    );
                    return $photo;
                }  
            }
        }
        
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

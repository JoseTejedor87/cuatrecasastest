<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\PublicationRepository;
use App\Repository\SectorRepository;
use App\Repository\PracticeRepository;
use App\Repository\OfficeRepository;
use App\Repository\EventRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Entity\Publication;
use App\Controller\Web\WebController;
use ORMBehaviors\Translatable\Translation;
use Doctrine\ORM\Query\Expr\Join;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;



class KnowledgeController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(ContainerBagInterface $params,CacheManager $imagineCacheManager)
    {
        $this->params = $params;
        $this->imagineCacheManager = $imagineCacheManager;
    }
    public function index(Request $request, PublicationRepository $publicationRepository,SectorRepository $sectorRepository,PracticeRepository $PracticeRepository,OfficeRepository $OfficeRepository, EventRepository $eventRepository)
    {
        $practices = $PracticeRepository->findAll();
        $sectors = $sectorRepository->findAll();
        $offices = $OfficeRepository->findAll();
        $types = $this->params->get('app.publications_types');
        $formats = $this->params->get('app.publications_format');
        
        $textSearch = $request->query->get('textSearch');
        $services = $request->query->get('services');
        $sector = $request->query->get('sector');
        $office = $request->query->get('office');
        $type = $request->query->get('type');
        $date = $request->query->get('date');
        $format = $request->query->get('format');
        $relatedEvents = $eventRepository->findByActivities('');
        $limit = 14;
        $page = $request->query->get('page') ?: 1;
        //dd($page);
        $query = $publicationRepository->createPublishedQueryBuilder('p');
        if($services){
            $query = $query->innerJoin('p.activities', 'a')
                           ->andWhere('a.id in (:activity)')
                           ->setParameter('activity', $services);
        }
        if($sector){
            $query = $query->innerJoin('p.activities', 's')
                           ->andWhere('s.id in (:sector)')
                           ->setParameter('sector', $sector);
        }
        if($office){
            $query = $query->innerJoin('p.offices', 'o')
                           ->andWhere('o.id in (:city)')
                           ->setParameter('city', $office);
        }
        if($type){
            $typeA = explode(",", $type);
            
            foreach ($typeA as $key => $value) {
                if($value == "news"){
                    $query = $query->orWhere('p INSTANCE OF App\Entity\News');
                }
                if($value == "article"){
                    $query = $query->orWhere('p INSTANCE OF App\Entity\Article');
                }
                if($value == "opinion"){
                $query = $query->orWhere('p INSTANCE OF App\Entity\Opinion');
                }
                if($value == "legalNovelty"){
                    $query = $query->orWhere('p INSTANCE OF App\Entity\LegalNovelty');
                }
                if($value == "research"){
                $query = $query->orWhere('p INSTANCE OF App\Entity\Research');
                }
            }
        }
        if ($format) {
            $query = $query->andWhere("p.format = :format")
                            ->setParameter('format', $format );
        }
        if ($textSearch) {
            $query = $query->innerJoin('p.publicationTranslation', 'pt', Join::ON, 'pt.translatable_id = p.id')
                            ->andWhere("pt.title LIKE :textSearch")
                            ->setParameter('textSearch', '%'.$textSearch .'%');
        }
        if($date){
            foreach ($date as $key => $value) {
                $startdate = new \DateTime($value.'-01-01');
                $enddate = new \DateTime($value.'-12-31');
                $query = $query->orWhere("p.publication_date BETWEEN '".$startdate->format('Y-m-d H:i:s')."' AND  '".$enddate->format('Y-m-d H:i:s')."'")->orderBy('p.publication_date', 'DESC');;
                
            }
            
        }else{
            $query = $query->andWhere('p.publication_date < :day')->setParameter('day', date("Y-m-d"))->orderBy('p.publication_date', 'DESC');
        }
        //dd( $query);
        
        $countPublications = count($query->getQuery()->getResult());
        $query = $query->setFirstResult($limit * ($page - 1))
                ->setMaxResults($limit)
                ->getQuery();
        $publications = $query->getResult();
        // dd($publications);
        if ($publications) {
            $pagesTotal = $countPublications/$limit;
            if (is_float($pagesTotal)) {
                $pagesTotal = intval($pagesTotal + 1);
            }
        }
                    


        foreach ($publications as $key => $value) {
            $value->fechaPubli = $value->getPublicationDate()->format("j F Y");
            if ($value instanceof \App\Entity\LegalNovelty || $value instanceof \App\Entity\Article || $value instanceof \App\Entity\Research){
                $value->type = 'article';
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

        if ($request->isXMLHttpRequest()) {
            $publicationsA = array();
            if (isset($publications)) {
                foreach ($publications as $key => $publication) {
                    // $url =  $this->container->get('router')->generate('publications_detail', array('slug' => $publication->translate('es')->getSlug() ? $publication->translate('es')->getSlug() : ''));
                    $publicationsA[$key] = array( 'title' => $publication->translate('es')->getTitle(), 'publicationType' => $publication->type, 'Slug' => $publication->translate('es')->getSlug(), 'Publication_date' => $publication->fechaPubli);
                    $publicationsA[$key]['photo'] = $publication->photo;
                }
            }
            $json = array(
                'publications' => $publicationsA,'countPublications' => isset($countPublications) ? $countPublications : 0,'pagesTotal' => isset($pagesTotal) ? $pagesTotal : 0 ,'page' => isset($page) ? $page : 0
            );
            if ($office) {
                $json['office'] = $office;
            }
            if ($textSearch) {
                $json['textSearch'] = $textSearch;
            }
            if ($services) {
                $json['services'] = $services;
            }
            if ($sector) {
                $json['sector'] = $sector;
            }
            if ($type) {
                $json['type'] = $type;
            }
            if ($format) {
                $json['format'] = $format;
            }
    
            return new JsonResponse($json);
        }else{
            return $this->render('web/knowledge/insights.html.twig', [
                'controller_name' => 'KnowledgeController',
                'sectors' => $sectors,
                'practices' => $practices,
                'offices' => $offices,
                'types' => $types,
                'formats' => $formats,
                'publications' => $publications,
                'relatedEvents' => $relatedEvents,
                'pagesTotal' => isset($pagesTotal) ? $pagesTotal : 0,
                'page' => $page,
            ]);
        }
        
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
    public function featured()
    {
        return $this->render('web/knowledge/featured.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }


    public function productDetailKnowledge()
    {
        return $this->render('web/knowledge/productDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    // TEMPORAL >>> BORRAR
    public function filter()
    {
        return $this->render('web/knowledge/filter.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }




}

<?php

namespace App\Controller\Web;

use App\Repository\LegislationRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\PublicationRepository;
use App\Repository\SectorRepository;
use App\Repository\PracticeRepository;
use App\Repository\OfficeRepository;
use App\Repository\InsightRepository;
use App\Repository\EventRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use App\Entity\Publication;
use App\Controller\Web\WebController;
use ORMBehaviors\Translatable\Translation;
use Doctrine\ORM\Query\Expr\Join;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use App\Controller\Web\NavigationService;

class KnowledgeController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(ContainerBagInterface $params, CacheManager $imagineCacheManager)
    {
        $this->params = $params;
        $this->imagineCacheManager = $imagineCacheManager;
    }
    public function index(
        Request $request,
        PublicationRepository $publicationRepository,
        SectorRepository $sectorRepository,
        InsightRepository $insightRepository,
        PracticeRepository $practiceRepository,
        ProductRepository $productRepository,
        OfficeRepository $officeRepository,
        LegislationRepository $legislationRepository,
        EventRepository $eventRepository,
        NavigationService $navigation
    ) {
        $practices = $practiceRepository->findBy(
            array(),
            array('highlighted' => 'asc')
        );
        $products = $productRepository->findBy(
            array(),
            array('highlighted' => 'asc')
        );
        $sectors = $sectorRepository->getSectorsByName($request)->getResult();
        $offices = $officeRepository->getOfficesByName($request)->getResult();
        $legislations = $legislationRepository->findBy(
            array(),
            array('name' => 'desc')
        );
        $insightsPrior = $insightRepository->getInsightsByName($request)->getResult();

        $insightsAll = $insightRepository->findBy(['showKnowledgeBlock' => true], ['id' => 'DESC']);
        $types = $this->params->get('app.publications_types');
        $formats = $this->params->get('app.publications_format');

        $textSearch = $request->query->get('textSearch');
        $services = $request->query->get('services');
        $product = $request->query->get('products');
        $sector = $request->query->get('sector');
        $office = $request->query->get('office');
        $legislation = $request->query->get('legislation');
        $type = $request->query->get('type');
        $date = $request->query->get('date');
        $format = $request->query->get('format');
        $collections = $request->query->get('collections');
        $initial = $request->query->get('initial');
        $relatedEvents = $eventRepository->findFeaturedByActivities('');
        $limit = 14;
        $page = $request->query->get('page') ?: 1;



        $query = $publicationRepository->createPublishedQueryBuilder('p');
        // $query = $query->innerJoin('p.translations', 'pt')
        //                     ->andWhere("pt.title != :textSearch")
        //                     ->setParameter('textSearch', '""');
        if ($services) {
            $servicesA = explode(",", $services);
            $query = $query->innerJoin('p.activities', 'a')
                           ->andWhere('a.id in (:activity)')
                           ->setParameter('activity', array_values($servicesA));
        }
        if ($product) {
            $productA = explode(",", $product);
            $query = $query->innerJoin('p.activities', 'a')
                ->andWhere('a.id in (:activity)')
                ->setParameter('activity', array_values($productA));
        }
        if ($sector) {
            $sectorA = explode(",", $sector);
            $query = $query->innerJoin('p.activities', 's')
                           ->andWhere('s.id in (:sector)')
                           ->setParameter('sector', array_values($sectorA));
        }
        if ($office) {
            $officeA = explode(",", $office);
            $query = $query->innerJoin('p.offices', 'o')
                           ->andWhere('o.id in (:city)')
                           ->setParameter('city', array_values($officeA));
        }
        if ($type) {
            $typeA = explode(",", $type);
            foreach ($typeA as $key => $value) {
                if ($value == "news") {
                    if (count($typeA)>1 && $key > 0) {
                        $query = $query->orWhere('p INSTANCE OF App\Entity\News');
                    } else {
                        $query = $query->andWhere('p INSTANCE OF App\Entity\News');
                    }
                }
                if ($value == "academy") {
                    if (count($typeA)>1 && $key > 0) {
                        $query = $query->orWhere('p INSTANCE OF App\Entity\Academy');
                    } else {
                        $query = $query->andWhere('p INSTANCE OF App\Entity\Academy');
                    }
                }
                if ($value == "opinion") {
                    if (count($typeA)>1 && $key > 0) {
                        $query = $query->orWhere('p INSTANCE OF App\Entity\Opinion');
                    } else {
                        $query = $query->andWhere('p INSTANCE OF App\Entity\Opinion');
                    }
                }
                if ($value == "legalNovelty") {
                    if (count($typeA)>1 && $key > 0) {
                        $query = $query->orWhere('p INSTANCE OF App\Entity\LegalNovelty');
                    } else {
                        $query = $query->andWhere('p INSTANCE OF App\Entity\LegalNovelty');
                    }
                }
            }
        }
        //dd($query->getQuery());
        if ($format) {
            $query = $query->andWhere("p.format = :format")
                            ->setParameter('format', $format);
        }
        if ($initial) {
            $query = $query->join('p.translations', 't')
                ->andWhere('t.title LIKE :titulo')
                ->setParameter('titulo', '%'.$initial.'%');
        }
        if ($collections) {
            $collectionsA = explode(",", $collections);
            $query = $query->innerJoin('p.insights', 'i')
                           ->andWhere('i.id in (:collections)')
                           ->setParameter('collections', array_values($collectionsA));
        }
        if ($legislation) {
            $legislationA = explode(",", $legislation);
            $query = $query->innerJoin('p.legislations', 'l')
                ->andWhere('l.id in (:legislation)')
                ->setParameter('legislation', array_values($legislationA));
        }
        if ($date) {
            $dateA = explode(",", $date);
            foreach ($dateA as $key => $value) {
                $startdate = new \DateTime($value.'-01-01');
                $enddate = new \DateTime($value.'-12-31');
                if ($key == 0) {
                    $query = $query->andWhere("p.publication_date BETWEEN '".$startdate->format('Y-m-d')."' AND  '".$enddate->format('Y-m-d')."'");
                } else {
                    $query = $query->orWhere("p.publication_date BETWEEN '".$startdate->format('Y-m-d')."' AND  '".$enddate->format('Y-m-d')."'");
                }
            }
            $query = $query->orderBy('p.publication_date', 'DESC');
        } else {
            $query = $query->andWhere('p.publication_date < :day')->setParameter('day', date("Y-m-d"))->orderBy('p.publication_date', 'DESC');
        }

        $qPriorizada = clone $query;

        // ZONE DE PRIORIZACION
        $place = $navigation->getParams()->get('app.office_place')[$navigation->getRegion()];
        if ($office) {
            $qPriorizada->andWhere('o.place = :place')
            ->setParameter('place', $place);
        } else {
            $qPriorizada->join('p.offices', 'o')
            ->andWhere('o.place = :place')
            ->setParameter('place', $place);
        }

        //---------------------
        $dateAux = new \DateTime();
        $dateAux->modify('-10 days');
        $rPriorizada = $qPriorizada->andWhere('p.publication_date > :date')
                                    ->setParameter('date', $dateAux)
                                    ->getQuery()
                                    ->getResult();


        // dd($rPriorizada);
        // no pude verificar que trajera alguna priorizada probar con datos reales

        $totalPublications = [];
        foreach ($rPriorizada as $key => $item) {
            $totalPublications[$item->getId()] = $item;
        }
        $totalInsights = [];
        foreach ($insightsPrior as $key => $item) {
            $totalInsights[$item->getId()] = $item;
        }

        $results = $query->getQuery()->getResult();


        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($results as $key => $item) {
            if (!isset($totalPublications[$item->getId()])) {
                array_push($totalPublications, $item);
            }
        }


        foreach ($insightsAll as $key => $item) {
            if (!isset($totalInsights[$item->getId()])) {
                array_push($totalInsights, $item);
            }
        }
        //dd($totalInsights);
        $insightsOrderToSend = [];
        foreach ($totalInsights as $key => $value) {
            array_push($insightsOrderToSend, $value);
        }


        $publications = array_slice($totalPublications, ($limit * ($page - 1)), $limit);

        if ($totalPublications) {
            $countPublications = count($totalPublications);
            $pagesTotal = $countPublications/$limit;
            if (is_float($pagesTotal)) {
                $pagesTotal = intval($pagesTotal + 1);
            }
        }


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
            $value->photo = $this->getPhotoPathByFilter($value, 'lawyers_grid',$navigation);
            if (!$value->photo) {
                $value->photo = '/web/assets/img/360x460_generica_news.jpg';
            }
        }
        //dd($publications);
        if ($request->isXMLHttpRequest()) {
            $publicationsA = array();
            if (isset($publications)) {
                foreach ($publications as $key => $publication) {
                    if ($publication->translate($navigation->getLanguage())->getTitle() != '') {
                        // $url =  $this->container->get('router')->generate('publications_detail', array('slug' => $publication->translate('es')->getSlug() ? $publication->translate('es')->getSlug() : ''));
                        $publicationsA[$key] = array( 'title' => $publication->translate($navigation->getLanguage())->getTitle(), 'publicationType' => $publication->type, 'Slug' => $publication->translate('es')->getSlug(), 'Publication_date' => $publication->fechaPubli);
                        $publicationsA[$key]['photo'] = $publication->photo;
                    }
                }
            }
            //dd($publicationsA);
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
            if ($product) {
                $json['products'] = $product;
            }
            if ($sector) {
                $json['sector'] = $sector;
            }
            if ($collections) {
                $json['collections'] = $collections;
            }
            if ($legislation) {
                $json['legislations'] = $legislation;
            }
            if ($type) {
                $json['type'] = $type;
            }
            if ($format) {
                $json['format'] = $format;
            }
            if ($initial) {
                $json['initial'] = $initial;
            }
            if ($date) {
                $json['date'] = $date;
            }
            if (empty($json['publications'])) {
                $json['error'] = "empty";
            }

            return new JsonResponse($json);

        } else {
            return $this->render('web/knowledge/index.html.twig', [
                'controller_name' => 'KnowledgeController',
                'sectors' => $sectors,
                'practices' => $practices,
                'products' => $products,
                'offices' => $offices,
                'types' => $types,
                'formats' => $formats,
                'publications' => $publications,
                'relatedEvents' => $relatedEvents,
                'legislations' => $legislations,
                'pagesTotal' => isset($pagesTotal) ? $pagesTotal : 0,
                'page' => $page,
                'insights' => $insightsOrderToSend
            ]);
        }
    }

}

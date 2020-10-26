<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\InsightRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\EventRepository;
use App\Repository\AwardRepository;
use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class InsightController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function index(AwardRepository $awardRepository, InsightRepository $insightRepository, EventRepository $eventRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $insights = $insightRepository->findAll();
        $relatedEvents = $eventRepository->findByActivities('');
        $relatedPublications = $publicationRepository->findByActivities('');

        $test = 'testc';
        return $this->render('web/insights/collections.html.twig', [
            'controller_name' => 'InsightController',
            'awards' => $awards,
            'testc' => $test,
            'relatedEvents' => $relatedEvents,
            'relatedPublications' => $relatedPublications,
            'insights' => $insights
        ]);
    }


    public function detail(Request $request, InsightRepository $insightRepository, CaseStudyRepository $caseStudyRepository, PublicationRepository $publicationRepository, EventRepository $eventRepository)
    {
        $insight = $insightRepository->getInstanceByRequest($request);
        $limit = 14;
        $page = $request->query->get('page') ?: 1;
        $query = $publicationRepository->createPublishedQueryBuilder('p');
        $query = $query->andWhere('p.publication_date < :day')->setParameter('day', date("Y-m-d"))->orderBy('p.publication_date', 'DESC');
        $countPublications = count($query->getQuery()->getResult());
        $query = $query->setFirstResult($limit * ($page - 1))
                ->setMaxResults($limit)
                ->getQuery();
        $publications = $query->getResult();
        foreach ($publications as $key => $value) {
            $value->fechaPubli = $value->getPublicationDate()->format("j F Y");
            if ($value instanceof \App\Entity\LegalNovelty || $value instanceof \App\Entity\Article || $value instanceof \App\Entity\Research) {
                $value->type = 'article';
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
        // TODO:
        // Revisar $insight y rellenar $contextualBlocks con las diferentes collecciones
        // de elementos en función del estado de los atributos $showKnowledgeBlock, $showEventsBlock,
        // $showLegalNoveltiesBlock y $showCaseStudiesBlock.
        //
        $relatedPublications = $publicationRepository->findByActivities($insight->getActivities());
        //dd($relatedPublications);
        $relatedEvents = $eventRepository->findByActivities($insight->getActivities());

        $contextualBlocks['cases'] = $caseStudyRepository->findByActivities($insight->getActivities()->toArray());
        $contextualBlocks['insights'] = $insightRepository->getPublishedRelatedInsights($insight);

        return $this->render('web/insights/detail.html.twig', [
            'insight' => $insight,
            'contextualBlocks' => $contextualBlocks,
            'publications' => $publications,
            'relatedEvents'=>$relatedEvents,
            'relatedPublications' => $relatedPublications
        ]);
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
}

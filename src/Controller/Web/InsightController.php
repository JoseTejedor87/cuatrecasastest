<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\InsightRepository;
use App\Repository\CaseStudyRepository;
use App\Controller\Web\WebController;

class InsightController extends WebController
{
    public function detail(Request $request, InsightRepository $insightRepository, CaseStudyRepository $caseStudyRepository)
    {
        $insight = $insightRepository->getInstanceByRequest($request);

        // TODO:
        // Revisar $insight y rellenar $contextualBlocks con las diferentes collecciones
        // de elementos en función del estado de los atributos $showKnowledgeBlock, $showEventsBlock,
        // $showLegalNoveltiesBlock y $showCaseStudiesBlock.
        //
        // $relatedPublications = $publicationRepository->findByActivities($insight->activities);
        // $relatedEvents = $eventRepository->findByActivities($insight->activities);

        $contextualBlocks['cases'] = $caseStudyRepository->findByActivities($insight->getActivities()->toArray());
        $contextualBlocks['insights'] = $insightRepository->getPublishedRelatedInsights($insight);

        return $this->render('web/insights/detail.html.twig', [
            'insight' => $insight,
            'contextualBlocks' => $contextualBlocks
        ]);
    }
}

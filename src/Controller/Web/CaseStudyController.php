<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\CaseStudyRepository;
use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;

class CaseStudyController extends WebController
{
    public function detail(Request $request, CaseStudyRepository $casestudyRepository, PublicationRepository $publicationRepository)
    {
        $casestudy = $casestudyRepository->getInstanceByRequest($request);
        $relatedPublications = $publicationRepository->findByActivities( $casestudy->getActivities());
        $relatedCaseStudies = $casestudyRepository->findByActivities(
            $casestudy->getActivities()->toArray(),
            $casestudy->getId()
        );

        return $this->render('web/casestudies/detail.html.twig', [
            'casestudy' => $casestudy,
            'relatedCaseStudies' => $relatedCaseStudies,
            'relatedPublications' => $relatedPublications
        ]);
    }
}

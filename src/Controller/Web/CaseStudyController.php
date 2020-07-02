<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\CaseStudyRepository;
use App\Controller\Web\WebController;

class CaseStudyController extends WebController
{
    public function detail(Request $request, CaseStudyRepository $casestudyRepository)
    {
        $casestudy = $casestudyRepository->getInstanceByRequest($request);

        $relatedCaseStudies = $casestudyRepository->getRelatedCasesByActivities($casestudy);

        return $this->render('web/casestudies/detail.html.twig', [
            'casestudy' => $casestudy,
            'relatedCaseStudies' => $relatedCaseStudies
        ]);
    }
}

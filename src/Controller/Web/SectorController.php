<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\SectorRepository;
use App\Repository\CaseStudyRepository;
use App\Controller\Web\WebController;

class SectorController extends WebController
{
    public function index(Request $request, SectorRepository $sectorRepository)
    {
        $sectors = $sectorRepository->createPublishedQueryBuilder('s')
            ->andwhere('s.highlighted = true')
            ->getQuery()
            ->getResult();

        return $this->render('web/sectors/index.html.twig', [
            'sectors' => $sectors,
        ]);
    }

    public function detail(Request $request, SectorRepository $sectorRepository, CaseStudyRepository $caseStudyRepository)
    {
        $sector = $sectorRepository->getInstanceByRequest($request);

        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$sector]
        );

        return $this->render('web/sectors/detail.html.twig', [
            'sector' => $sector,
            'relatedCaseStudies' => $relatedCaseStudies
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\SectorRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
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

    public function detail(Request $request, SectorRepository $sectorRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository)
    {
        $awards = $awardRepository->getAll();
        $sector = $sectorRepository->getInstanceByRequest($request);

        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$sector]
        );

        $awardsFiltered = [];
        foreach ($awards as $award)
        {
            foreach($award->getActivities() as $activity){
                if ( $activity instanceof \App\Entity\Sector)
                {
                    array_push($awardsFiltered,$award);
                    break;
                }
            }
        }

        return $this->render('web/sectors/detail.html.twig', [
            'sector' => $sector,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awardsFiltered
        ]);
    }
}

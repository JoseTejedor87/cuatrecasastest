<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\DeskRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Controller\Web\WebController;

class DeskController extends WebController
{
    public function index(Request $request, DeskRepository $deskRepository)
    {
        $desks = $deskRepository->createPublishedQueryBuilder('d')
            ->andwhere('d.highlighted = true')
            ->getQuery()
            ->getResult();

        return $this->render('web/desks/index.html.twig', [
            'desks' => $desks,
        ]);
    }

    public function detail(Request $request, DeskRepository $deskRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository)
    {
        $awards = $awardRepository->getAll();
        $desk = $deskRepository->getInstanceByRequest($request);
        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$desk]
        );
        // echo $awards[0]->getImage()->getFileName(); die();

        return $this->render('web/desks/detail.html.twig', [
            'desk' => $desk,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awards
        ]);
    }
}

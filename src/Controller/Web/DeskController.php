<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\DeskRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;

class DeskController extends WebController
{
    public function index(Request $request, DeskRepository $deskRepository)
    {
        /*
        $desks = $deskRepository->createPublishedQueryBuilder('d')
            ->andwhere('d.highlighted = true')
            ->getQuery()
            ->getResult();
        */

        $desks = $deskRepository->getDeskByName($request)->getResult();            

        return $this->render('web/desks/index.html.twig', [
            'desks' => $desks,
        ]);
    }


    public function detail(Request $request, DeskRepository $deskRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $desk = $deskRepository->getInstanceByRequest($request);
        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$desk]
        );
        $relatedPublications = $publicationRepository->findByActivities([$desk]);
        $awardsFiltered = [];
        foreach ($awards as $award)
        {
            foreach($award->getActivities() as $activity){
                if ( $activity instanceof \App\Entity\Desk)
                {
                    array_push($awardsFiltered,$award);
                    break;
                }
            }
        }

        return $this->render('web/desks/detail.html.twig', [
            'desk' => $desk,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awardsFiltered,
            'relatedPublications' => $relatedPublications
        ]);
    }
}

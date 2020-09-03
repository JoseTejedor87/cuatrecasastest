<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;

class PracticeController extends WebController
{
    public function index(Request $request, PracticeRepository $practiceRepository, PublicationRepository $publicationRepository)
    {
        /*
        $practices = $practiceRepository->createPublishedQueryBuilder('p')
            ->andwhere('p.highlighted = true')
            ->getQuery()
            ->getResult();
        */
        $practices = $practiceRepository->getPracticeByName($request)->getResult();
        $relatedPublications = $publicationRepository->findByActivities('');

        return $this->render('web/practices/index.html.twig', [
            'practices' => $practices,
            'relatedPublications' => $relatedPublications
        ]);
    }

    public function detail(Request $request, PracticeRepository $practiceRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $practice = $practiceRepository->getInstanceByRequest($request);

        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$practice]
        );
        $relatedPublications = $publicationRepository->findByActivities([$practice]);
        $awardsFiltered = [];
        foreach ($awards as $award)
        {
            foreach($award->getActivities() as $activity){
                if ( $activity instanceof \App\Entity\Practice)
                {
                    array_push($awardsFiltered,$award);
                    break;
                }
            }
        }
        
        return $this->render('web/practices/detail.html.twig', [
            'practice' => $practice,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awardsFiltered,
            'relatedPublications' => $relatedPublications
        ]);
    }
}

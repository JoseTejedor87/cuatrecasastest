<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\SectorRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;

class SectorController extends WebController
{
    public function index(Request $request, SectorRepository $sectorRepository, PublicationRepository $publicationRepository)
    {
        $sectors = $sectorRepository->getSectorsByName($request)->getResult();
        $relatedPublications = $publicationRepository->findByActivities([]);

        return $this->render('web/sectors/index.html.twig', [
            'sectors' => $sectors,
            'relatedPublications' => $relatedPublications
        ]);
    }

    public function detail(Request $request, SectorRepository $sectorRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $sector = $sectorRepository->getInstanceByRequest($request);
        $key_contacts = $sector->getKeyContacts();
        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$sector]
        );
        $relatedPublications = $publicationRepository->findByActivities([$sector]);
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
            'key_contacts' => $key_contacts,
            'relatedCaseStudies' => $relatedCaseStudies,
            'relatedPublications' => $relatedPublications,
            'awards' => $awardsFiltered
        ]);
    }
}

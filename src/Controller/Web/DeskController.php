<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\DeskRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;
use App\Repository\GeneralBlockRepository;

class DeskController extends WebController
{
    public function index(Request $request, DeskRepository $deskRepository, GeneralBlockRepository $generalBlockRepository)
    {
        /*
        $desks = $deskRepository->createPublishedQueryBuilder('d')
            ->andwhere('d.highlighted = true')
            ->getQuery()
            ->getResult();
        */
        $blockCareer = $generalBlockRepository->findOneBy(['blockName' => 'block_career']);
        $desks = $deskRepository->getDeskByName($request)->getResult();
        $hideTitle = true;
        return $this->render('web/desks/index.html.twig', [
            'desks' => $desks,
            'career' => $blockCareer,
            'hideTitle' => $hideTitle
        ]);
    }


    public function detail(Request $request, DeskRepository $deskRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $desk = $deskRepository->getInstanceByRequest($request);
        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$desk]
        );
        $key_contacts = $desk->getKeyContacts();
        $relatedPublications = $publicationRepository->findByActivities([$desk]);
        $awardsFiltered = [];
        foreach ($awards as $award) {
            foreach ($award->getActivities() as $activity) {
                if ($activity instanceof \App\Entity\Desk) {
                    array_push($awardsFiltered, $award);
                    break;
                }
            }
        }

        return $this->render('web/desks/detail.html.twig', [
            'desk' => $desk,
            'key_contacts' => $key_contacts,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awardsFiltered,
            'relatedPublications' => $relatedPublications
        ]);
    }
}

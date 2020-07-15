<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;
use App\Repository\CaseStudyRepository;


class PublicationController extends WebController
{
    public function detail(Request $request, PublicationRepository $PublicationRepository,CaseStudyRepository $caseStudyRepository)
    {
        $publication = $PublicationRepository->getInstanceByRequest($request);
        $caseStudiesRelated = $caseStudyRepository->findByActivities($publication->getActivities()->toArray());
        

        return $this->render('web/knowledge/articleDetail.html.twig', [
            'publication' => $publication,
            'caseStudiesRelated'  => $caseStudiesRelated,
        ]);
    }
}

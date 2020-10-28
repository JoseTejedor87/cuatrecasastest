<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;
use App\Repository\CaseStudyRepository;
use App\Controller\Web\NavigationService;

class PublicationController extends WebController
{
    public function detail(Request $request, PublicationRepository $PublicationRepository,CaseStudyRepository $caseStudyRepository, NavigationService $navigation)
    {
        $publication = $PublicationRepository->getInstanceByRequest($request);
        $caseStudiesRelated = $caseStudyRepository->findByActivities($publication->getActivities()->toArray());
        $relatedPublications = $PublicationRepository->findByActivities($publication->getActivities());
        $attachmentPublished = [];
        foreach($publication->getAttachments() as $attachment)
        {
            //dd($attachment);
            if($attachment->getType() == 'publication_main_photo' ||  $attachment->getType() == 'article_main_photo'){

                $publication->photo = $attachment->getFileName();
            }

                if ($attachment->isPublished($navigation->getLanguage(),$navigation->getRegion()))
                array_push($attachmentPublished,$attachment);
        }


        return $this->render('web/publication/detail.html.twig', [
            'publication' => $publication,
            'caseStudiesRelated'  => $caseStudiesRelated,
            'attachmentPublished' => $attachmentPublished,
            'relatedPublications' => $relatedPublications
        ]);
    }
}

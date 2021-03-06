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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;


class PublicationController extends WebController
{

    protected $imagineCacheManager;

    public function __construct( CacheManager $imagineCacheManager)
    {

        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function detail(Request $request, PublicationRepository $PublicationRepository,CaseStudyRepository $caseStudyRepository, NavigationService $navigation)
    {
        $publication = $PublicationRepository->getInstanceByRequest($request);
        $caseStudiesRelated = $caseStudyRepository->findByActivities($publication->getActivities()->toArray());
        $relatedPublications = $PublicationRepository->findByActivities($publication->getActivities());
        $attachmentPublished = [];
        $headerImage = '';
        $headerImage = $this->getPhotoPathByFilter($publication, 'full_header',$navigation);
        $lang = $navigation->getLanguage();

        foreach($publication->getAttachments() as $attachment)
        {
            if($attachment->getType() == 'publication_main_photo'){

                $publication->photo = $attachment->getFileName();
            }

            if ($attachment->isPublished($lang,$navigation->getRegion()))
            array_push($attachmentPublished,$attachment);
        }
        
        // CUANDO SEAN PUBLICACIONES QUE SOLO SON PDF LOS REDIRIGE AUTOMATICAMENTE
        if($publication->translate($lang)->getSummary() == '' && $publication->translate($lang)->getContent() == ''){
            foreach($attachmentPublished as $attachment){
                if( in_array($lang,$attachment->getLanguages()) && in_array($navigation->getRegion(),$attachment->getRegions()))
                {
                    return $this->redirect($request->getSchemeAndHttpHost().'/resources/'.$attachment->getFilename());
                }
            }
        }

        return $this->render('web/publication/detail.html.twig', [
            'publication' => $publication,
            'caseStudiesRelated'  => $caseStudiesRelated,
            'attachmentPublished' => $attachmentPublished,
            'relatedPublications' => $relatedPublications,
            'headerImage' => $headerImage
        ]);
    }    
}
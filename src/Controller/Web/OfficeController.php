<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Web\WebController;
use App\Repository\OfficeRepository;
use App\Repository\InsightRepository;
use App\Repository\PublicationRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\HomeRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use App\Controller\Web\NavigationService;


class OfficeController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function detail(Request $request, OfficeRepository $officeRepository, InsightRepository $insightRepository, HomeRepository $homeRepository,
    CaseStudyRepository $caseStudyRepository, PublicationRepository $publicationRepository, NavigationService $navigation)
    {
        $office = $officeRepository->getInstanceByRequest($request);
        // Los casos son los Insight que corresponden a cada uno de los abogados.
        $home = $homeRepository->findOneBy(['id' => 1]);

        $lawyersId = [];
        foreach ($office->getLawyer() as $key => $value) {
            if( $value->getId() != null && $value->getId() != '' ){
                array_push($lawyersId,$value->getId() );
            }
        }
        //  $insights = $insightRepository->getInsightByLawyers($lawyersId);

        // Casos relacionadas con abogados de las oficinas
        $caseStudies = $caseStudyRepository->findByLawyersId($lawyersId);
        //  dd($caseStudies);

        
        // Publicaciones relacionadas con las oficians son los eventos y publicaciones 
        $relatedPublications = $publicationRepository->setTypePublication($office->getPublication());       

        $relatedEvents = $office->getEvent();

        return $this->render('web/shared/office-detail.html.twig', [
            'office' => $office,
            'relatedPublications' => $relatedPublications,
            // 'relatedEvents' => $relatedEvents, // no se estan usando Consultar si se debe agregar como contenido relacionado tambien
            'home' => $home,
            'caseStudies' => $caseStudies
        ]);
    }


    public function index(Request $request,OfficeRepository $officeRepository, NavigationService $navigation)
    {
        return $officeRepository->getInstanceByRequest($request);
    }

}

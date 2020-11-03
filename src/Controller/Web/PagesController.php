<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;
use App\Repository\OfficeRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\HomeRepository;
use App\Repository\PageRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\NavigationService;

class PagesController extends WebController
{
    public function detail(Request $request, PageRepository $PageRepository, CaseStudyRepository $caseStudyRepository,OfficeRepository $OfficeRepository,
     HomeRepository $homeRepository, NavigationService $navigation, PublicationRepository $publicationRepository)
    {
        $page = $PageRepository->getInstanceByRequest($request);
        $home = $homeRepository->findOneBy(['id' => 1]);
        $relatedPublications = $publicationRepository->findByActivities('');
        if ($page->getCustomTemplate() == 'location/others'){
            $place = $navigation->getParams()->get('app.office_place')['global'];
        }else{
            $place = $navigation->getParams()->get('app.office_place')[$page->getCustomTemplate()] ?? null ;
        }
        
        $cases = $caseStudyRepository->findCasesByRegion($place);
        
        $urlTemplate = 'empty';
        if (null !== $page->getCustomTemplate() && $page->getCustomTemplate() != '') {
            $urlTemplate = 'custom/'.$page->getCustomTemplate();
            if ($page->getCustomTemplate() =="location/vision") {
                $officeA = array();
                $officeATest = array();
                $offices = $OfficeRepository->createQueryBuilder('o')
                ->where("o.lat != ''")
                //  ->orderBy('o.country', 'DESC')  PAra ordenarlos hay que hacerlo contra OfficeTranstable.country
                ->getQuery()->getResult();
                foreach ($offices as $key => $office) {
                    array_push($officeA, '<h5 id="'.$office->getId().'">'.$office->translate($navigation->getLanguage())->getCountry().'</h5><h6>'.$office->translate($navigation->getLanguage())->getCity().'</h6><p>'.$office->getAddress().'</p>');
                    array_push($officeATest, [ "lat" => floatval($office->getLat()),"lng" =>  floatval($office->getLng())]);
                }
            }
        }
        $Publicaciones = $page->getPublication()->toArray();
        $Publicaciones = array_reverse($Publicaciones);
        foreach ($Publicaciones as $key => $Publication) {
            foreach ($relatedPublications as $key1 => $relatedPublication) {
                if($Publication->getId() == $relatedPublication->getId()){
                     unset($relatedPublications[$key1]);
                } 
            }
            array_unshift( $relatedPublications ,  $Publication);
        }
        $relatedPublications = array_values($relatedPublications);
        //dd($page->getBlocks()[0]);

        return $this->render('web/pages/'.$urlTemplate.'.html.twig', [
            'officesMapa' => isset($officeATest) ? json_encode($officeATest)  : '',
            'officesMapaLabel' => isset($officeA) ? json_encode($officeA)  : '',
            'offices' => isset($offices) ? $offices  : '',
            'page' => $page,
            'controller_name' => 'PageController',
            'home' => $home,
            'caseStudies' => $cases,
            'relatedPublications' => $relatedPublications,
        ]);
    }
}

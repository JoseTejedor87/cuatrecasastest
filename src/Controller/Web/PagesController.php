<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;
use App\Repository\OfficeRepository;
use App\Repository\HomeRepository;
use App\Repository\PageRepository;
use App\Controller\Web\NavigationService;

class PagesController extends WebController
{

    public function detail(Request $request, PageRepository $PageRepository, OfficeRepository $OfficeRepository, HomeRepository $homeRepository, NavigationService $navigation)
    {
        $page = $PageRepository->getInstanceByRequest($request);
        $home = $homeRepository->findOneBy(['id' => 1]);
        
        $urlTemplate = 'empty';
        if (null !== $page->getCustomTemplate() && $page->getCustomTemplate() != '')
        {
            $urlTemplate = 'custom/'.$page->getCustomTemplate();
            if($page->getCustomTemplate() =="vision"){
                $officeA = array();
                $officeATest = array();
                $offices = $OfficeRepository->createQueryBuilder('o')
                ->where("o.lat != ''")
                //  ->orderBy('o.country', 'DESC')  PAra ordenarlos hay que hacerlo contra OfficeTranstable.country
                ->getQuery()->getResult();
                foreach ($offices as $key => $office) {
                    array_push($officeA,  '<h5>'.$office->getCountry().'</h5><h6>'.$office->translate($navigation->getLanguage())->getCity().'</h6><p>'.$office->getAddress().'</p>');
                    array_push($officeATest, [ "lat" => floatval($office->getLat()),"lng" =>  floatval($office->getLng())]);
                }
            }
        }

        //dd($page->getBlocks()[0]);

        return $this->render('web/pages/'.$urlTemplate.'.html.twig', [
            'officesMapa' => isset($officeATest) ? json_encode($officeATest)  : '',
            'officesMapaLabel' => isset($officeA) ? json_encode($officeA)  : '',
            'offices' => isset($offices) ? $offices  : '',
            'page' => $page,
            'controller_name' => 'PageController',
            'home' => $home
        ]);
    }
}

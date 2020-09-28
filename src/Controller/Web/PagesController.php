<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;
use App\Repository\OfficeRepository;

use App\Repository\PageRepository;

class PagesController extends WebController
{

    public function detail(Request $request, PageRepository $PageRepository, OfficeRepository $OfficeRepository)
    {
        $page = $PageRepository->getInstanceByRequest($request);

        $urlTemplate = 'empty';
        if (null !== $page->getCustomTemplate() && $page->getCustomTemplate() != '')
        {
            $urlTemplate = 'custom/'.$page->getCustomTemplate();
            if($page->getCustomTemplate() =="vision"){
                $officeA = array();
                $officeATest = array();
                $offices = $OfficeRepository->createQueryBuilder('o')
                ->where("o.lat != ''")
                ->getQuery()->getResult();
                foreach ($offices as $key => $office) {
                    array_push($officeA, [ $office->getCity(),floatval($office->getLat()),floatval($office->getLng()),$office->getId()]);
                    array_push($officeATest, [ "lat" => floatval($office->getLat()),"lng" =>  floatval($office->getLng())]);
                }
            }
        }

        return $this->render('web/pages/'.$urlTemplate.'.html.twig', [
            'officesMapa' => isset($officeA) ? json_encode($officeA)  : '',
            'offices' => isset($offices) ? $offices  : '',
            'page' => $page,
            'controller_name' => 'PageController'
        ]);
    }
}

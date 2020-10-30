<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Repository\BannerRepository;
use App\Repository\SliderRepository;
use App\Repository\HomeRepository;
use App\Repository\GeneralBlockRepository;
use App\Controller\Web\WebController;
use App\Controller\Web\NavigationService;

class HomeController extends WebController
{
    public function index(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository,
             PublicationRepository $publicationRepository, BannerRepository $bannerRepository, SliderRepository $sliderRepository,
              HomeRepository $homeRepository, GeneralBlockRepository $generalBlockRepository, NavigationService $navigation )
    {
        $bannerHome = $bannerRepository->findOneBy(['location' => 'home']);

        $slidesOrdered = [];
        $slidesPrior = $sliderRepository->getAllByPriorityRegion($bannerHome->getId());
        $slidesAll = $sliderRepository->getAllByPriority($bannerHome->getId());

        // se evitan  posisiones que pueden repetirse y se agrean al final el resto
        foreach ($slidesPrior as $key => $item) {
            $slidesOrdered[$item->getId()] = $item;
        }
        foreach ($slidesAll as $key => $item) {
            if (!isset($slidesOrdered[$item->getId()])){
                array_push($slidesOrdered, $item);
            }
        }
        $sliderCorrect_ID_Order = [];
        foreach ($slidesOrdered as $value) {
            array_push($sliderCorrect_ID_Order, $value);
        }
        //dd($sliderCorrect_ID_Order);

        $request->attributes->set('id', 1);
        $home = $homeRepository->getInstanceByRequest($request);



        $events = $EventRepository->findBy([], ['startDate' => 'DESC'], 5);
        $blockCareer = $generalBlockRepository->findOneBy(['blockName' => 'block_career']);


        $relatedPublications = $publicationRepository->findByActivities('');

        //dd($relatedPublications);
        /*
        foreach($home->getInsights()[0]->getActivities() as $item){
            print_r($item->translate('es')->getTitle()); die();
        }
        */

        return $this->render('web/home/index.html.twig', [
            'events' => $events,
            'relatedPublications' => $relatedPublications,
            'banner' => $bannerHome,
            'slidesOrdered' => $sliderCorrect_ID_Order,
            'home' => $home,
            'careerBlock' => $blockCareer
        ]);
    }
<<<<<<< HEAD
=======


    // NO TOCAR PLIS, ES UNA MAQUETA NECESARIA DE FRONT QUE NO NECESITA PROGRAMACION
    public function components()
    {
        return $this->render('web/home/components.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }

    public function institutoRRHH()
    {
        return $this->render('web/home/institutoRRHH.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }

    public function politicaCookies()
    {
        return $this->render('web/home/politicaCookies.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
>>>>>>> 21045137d3b215e954bd6748154f30628a05d649
}

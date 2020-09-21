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
use App\Controller\Web\WebController;


class HomeController extends WebController
{
    public function index(Request $request, EventTranslationRepository $EventTranslationRepository,
     EventRepository $EventRepository, PublicationRepository $publicationRepository, BannerRepository $bannerRepository, SliderRepository $sliderRepository)
    {
        $bannerHome = $bannerRepository->findOneBy(['location' => 'home']);
        //$slidesOrdered = $sliderRepository->findBy(['banners' => $bannerHome->getId()], ['priority' => 'ASC']);
        

        $slidesOrdered = $sliderRepository->getAllByPriority($bannerHome->getId());
        // $slidesOrdered = $sliderRepository->findAll();

        
        //dd($slidesOrdered); die();        
        //dd(($bannerHome->getSliders()[0]->getImage())->getFileName());  die();
        $events = $EventRepository->findBy([], ['startDate' => 'DESC'], 5);
        $relatedPublications = $publicationRepository->findByActivities('');
        return $this->render('web/home/index.html.twig', [
            'events' => $events,
            'relatedPublications' => $relatedPublications,
            'banner' => $bannerHome,
            'slidesOrdered' => $slidesOrdered
        ]);
    }


    public function components(AwardRepository $awardRepository)
    {
        $awards = $awardRepository->getAll();
        return $this->render('web/home/components.html.twig', [
            'controller_name' => 'HomeController',
            'awards' => $awards
        ]);
    }

    public function userSettings()
    {
        return $this->render('web/home/userSettings.html.twig', [
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
}

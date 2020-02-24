<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sector;
use App\Repository\SectorRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;
/**
* @Route("/{idioma}/activity", name="activity", methods={"GET"})
*/
class ActivityController extends WebController
{
    /**
     * @Route("/sectorsHome", name="sectorsHome")
     */
    public function sectorsHome(Request $request,SectorRepository $sectorRepository)
    {
        $sectors = $sectorRepository->findAll();
        $this->isThisLocale($request, $request->attributes->get('idioma'));

        return $this->render('web/activity/sectorsHome.html.twig', [
            'controller_name' => 'ActivityController',
            'sectors' => $sectors, 
        ]);
    }

    /**
     * @Route("/consumptionRetail/{slug}", name="consumptionRetail")
     */
    public function consumptionRetail(Request $request,SectorRepository $sectorRepository, ActivityTranslationRepository $ActivityTranslationRepository)
    {
        $ActivityTranslation = $ActivityTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $sector = $sectorRepository->findOneBy(['id' => $ActivityTranslation->getTranslatable()->getId()]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        //dd($sector->getEvents());
        return $this->render('web/activity/consumptionRetail.html.twig', [
            'controller_name' => 'ActivityController',
            'sector' => $sector, 
        ]);
    }

    /**
     * @Route("/successStories", name="successStories")
     */
    public function successStories()
    {
        return $this->render('web/activity/successStories.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}

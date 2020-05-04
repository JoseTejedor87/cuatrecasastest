<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sector;
use App\Repository\SectorRepository;
use App\Repository\ActivityRepository;
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
    public function sectorsHome(Request $request,SectorRepository $sectorRepository,ActivityRepository $ActivityRepository)
    {
        // $activity =  $ActivityRepository->find(1);
        // echo(var_dump($activity));
        // $activityhijo =  $ActivityRepository->find(8);
        // $activity->addRelatedActivity($activityhijo);
        // $ActivityRepository->persist($activity);
        // $ActivityRepository->flush();
        // echo(var_dump($activity));
        // die();



        $sectors = $sectorRepository->findAll();
        $this->isThisLocale($request, $request->attributes->get('idioma'));

        return $this->render('web/activity/sectorsHome.html.twig', [
            'controller_name' => 'ActivityController',
            'sectors' => $sectors,
        ]);
    }

    /**
     * @Route("/sectorDetail/{slug}", name="sectorDetail")
     */
    public function sectorDetail(Request $request,SectorRepository $sectorRepository, ActivityTranslationRepository $ActivityTranslationRepository)
    {
        $ActivityTranslation = $ActivityTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $sector = $sectorRepository->findOneBy(['id' => $ActivityTranslation->getTranslatable()->getId()]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/activity/sectorDetail.html.twig', [
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

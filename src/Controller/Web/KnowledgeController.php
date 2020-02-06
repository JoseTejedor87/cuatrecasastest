<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;

use App\Controller\Web\WebController;


/**
* @Route("/{idioma}/knowledge", name="knowledge")
*/
class KnowledgeController extends WebController
{
    /**
     * @Route("/insights", name="insights")
     */
    public function insights()
    {
        return $this->render('web/knowledge/insights.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    /**
     * @Route("/eventDetail/{slug}", name="eventDetail")
     */
    public function eventDetail(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository)
    {
        $EventTranslation = $EventTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $event = $EventRepository->findOneBy(['id' => $EventTranslation->getTranslatable()->getId()]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        //dd($EventTranslation);
        return $this->render('web/knowledge/eventDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
            'event' => $event,
        ]);
    }
}

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
* @Route("/home", name="home")
*/
class HomeController extends WebController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository)
    {
        $events = $EventRepository->findBy([], ['startDate' => 'DESC'], 5);
        // $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/home/index.html.twig', [
            'controller_name' => 'HomeController',
            'events' => $events,
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;
use App\Controller\Web\WebController;

class HomeController extends WebController
{
    public function index(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository)
    {
        $events = $EventRepository->findBy([], ['startDate' => 'DESC'], 5);
        return $this->render('web/home/index.html.twig', [
            'events' => $events,
        ]);
    }

    public function components()
    {
        return $this->render('web/home/components.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
}

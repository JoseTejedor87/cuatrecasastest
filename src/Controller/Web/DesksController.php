<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\DeskRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

class DesksController extends WebController
{
    public function index(Request $request, DeskRepository $deskRepository)
    {
        $desks = $deskRepository->createPublishedQueryBuilder('d')
            ->andwhere('d.highlighted = true')
            ->getQuery()
            ->getResult();

        return $this->render('web/desks/index.html.twig', [
            'desks' => $desks,
        ]);
    }

    public function detail(Request $request, DeskRepository $deskRepository)
    {
        $desk = $deskRepository->getInstanceByRequest($request);

        return $this->render('web/desks/detail.html.twig', [
            'desk' => $desk,
        ]);
    }
}

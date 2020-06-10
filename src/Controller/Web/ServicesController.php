<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\DeskRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

class ServicesController extends WebController
{
    public function desks(Request $request, DeskRepository $DeskRepository)
    {
        $desks = $DeskRepository->findAll();
        return $this->render('web/services/desks.html.twig', [
            'controller_name' => 'ServicesController',
            'desks' => $desks,
        ]);
    }

    public function productDetail()
    {
        return $this->render('web/services/productDetail.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }
}

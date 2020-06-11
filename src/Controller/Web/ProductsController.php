<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\DeskRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

class ProductsController extends WebController
{
    public function index(Request $request, DeskRepository $DeskRepository)
    {
        return $this->render('web/services/products.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

}

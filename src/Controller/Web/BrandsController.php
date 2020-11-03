<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GeneralBlockRepository;
use App\Controller\Web\WebController;
use App\Controller\Web\NavigationService;

class BrandsController extends WebController
{
    public function institutoRRHH()
    {
        return $this->render('web/brands/institutoRRHH.html.twig', [
            'controller_name' => 'BrandsController'
        ]);
    }
}

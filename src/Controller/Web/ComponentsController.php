<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GeneralBlockRepository;
use App\Controller\Web\WebController;
use App\Controller\Web\NavigationService;

class ComponentsController extends WebController
{
    # No tocar, es una maqueta de referencia que no necesita programación
    public function index()
    {
        return $this->render('web/components/index.html.twig', [
            'controller_name' => 'ComponentsController'
        ]);
    }
}

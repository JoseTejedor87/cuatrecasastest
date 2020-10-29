<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GeneralBlockRepository;
use App\Controller\Web\WebController;
use App\Controller\Web\NavigationService;

class PrivacyController extends WebController
{

    public function politicaCookies()
    {
        return $this->render('web/privacy/politicaCookies.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }
}

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
    public function siteMap()
    {
        return $this->render('web/privacy/siteMap.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }

    public function webUse()
    {
        return $this->render('web/privacy/webUse.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }

    public function privacyPolicy()
    {
        return $this->render('web/privacy/privacyPolicy.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }

    public function cookiesPolicy()
    {
        return $this->render('web/privacy/cookiesPolicy.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }

    public function securityPolicy()
    {
        return $this->render('web/privacy/securityPolicy.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }

    public function contactUs()
    {
        return $this->render('web/privacy/contactUs.html.twig', [
            'controller_name' => 'PrivacyController'
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;

class FirmController extends WebController
{
    public function vision()
    {
        return $this->render('web/firm/vision.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }

    public function social()
    {
        return $this->render('web/firm/social.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }

    public function environment()
    {
        return $this->render('web/firm/environment.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }

    public function governance()
    {
        return $this->render('web/firm/governance.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }

    public function innovation()
    {
        return $this->render('web/firm/innovation.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }

    public function mediaCenter()
    {
        return $this->render('web/firm/media-center.html.twig', [
            'controller_name' => 'FirmController'
        ]);
    }
}

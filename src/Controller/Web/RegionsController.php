<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;

class RegionsController extends WebController
{
    public function vision()
    {
        return $this->render('web/pages/custom/vision.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function spain()
    {
        return $this->render('web/pages/custom/spain.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function portugal()
    {
        return $this->render('web/pages/custom/portugal.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function latam()
    {
        return $this->render('web/pages/custom/latam.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function others()
    {
        return $this->render('web/pages/custom/others.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function detailOffice()
    {
        return $this->render('web/pages/custom/office-detail.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function detailOthers()
    {
        return $this->render('web/pages/custom/office-others.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }
}

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
        return $this->render('web/regions/vision.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function spain()
    {
        return $this->render('web/regions/spain.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function portugal()
    {
        return $this->render('web/regions/portugal.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function latam()
    {
        return $this->render('web/regions/latam.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function others()
    {
        return $this->render('web/regions/others.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function detailOffice()
    {
        return $this->render('web/regions/office-detail.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }

    public function detailOthers()
    {
        return $this->render('web/regions/office-others.html.twig', [
            'controller_name' => 'RegionsController'
        ]);
    }
}

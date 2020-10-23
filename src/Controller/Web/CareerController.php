<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;

class CareerController extends WebController
{
    public function career()
    {
        return $this->render('web/career/career.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }

    public function live()
    {
        return $this->render('web/career/live-cuatrecasas.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }

    public function revolution()
    {
        return $this->render('web/career/revolution.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }

    public function learn()
    {
        return $this->render('web/career/learn.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }

    public function international()
    {
        return $this->render('web/career/international.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }

    public function join()
    {
        return $this->render('web/career/join-us.html.twig', [
            'controller_name' => 'CareerController'
        ]);
    }
}

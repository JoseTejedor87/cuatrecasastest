<?php

namespace App\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;


class dashboardController extends AbstractController
{

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        $session = new Session();
        $user = $session->get('User');
       // dd((array)$user);
        return $this->render('cms/dashboard/index.html.twig', [
            'controller_name' => 'dashboardController',
            'user' => (array)$user,
        ]);
    }
}

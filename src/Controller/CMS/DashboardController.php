<?php

namespace App\Controller\CMS;

use App\Entity\Lawyer;
use App\Controller\CMS\CMSController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashboardController extends CMSController
{

    /**
     * @Route("cms", name="dashboard")
     */
    public function index(ContainerInterface $container)
    {
        return $this->render('cms/dashboard/index.html.twig', [
            'controller_name' => 'dashboardController',
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/activity", name="activity")
*/
class ActivityController extends AbstractController
{
    /**
     * @Route("/sectorsHome", name="sectorsHome")
     */
    public function sectorsHome()
    {
        return $this->render('web/activity/sectorsHome.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }

    /**
     * @Route("/consumptionRetail", name="consumptionRetail")
     */
    public function consumptionRetail()
    {
        return $this->render('web/activity/consumptionRetail.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }

    /**
     * @Route("/successStories", name="successStories")
     */
    public function successStories()
    {
        return $this->render('web/activity/successStories.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}

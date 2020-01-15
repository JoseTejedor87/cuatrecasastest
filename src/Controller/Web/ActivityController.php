<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/activity", name="Activity")
*/
class ActivityController extends AbstractController
{
    /**
     * @Route("/sectoresHome", name="sectoresHome")
     */
    public function sectoresHome()
    {
        return $this->render('web/activity/sectoresHome.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }

    /**
     * @Route("/consumoRetail", name="consumoRetail")
     */
    public function consumoRetail()
    {
        return $this->render('web/activity/consumoRetail.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}

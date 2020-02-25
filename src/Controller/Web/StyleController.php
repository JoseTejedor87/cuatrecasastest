<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/guide", name="guide")
*/
class StyleController extends AbstractController
{
    /**
     * @Route("/style", name="style")
     */
    public function style()
    {
        return $this->render('web/guide/style.html.twig', [
            'controller_name' => 'StyleController',
        ]);
    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function calendar()
    {
        return $this->render('web/guide/calendar.html.twig', [
            'controller_name' => 'StyleController',
        ]);
    }

    /**
     * @Route("/daterange", name="daterange")
     */
    public function daterange()
    {
        return $this->render('web/guide/daterange.html.twig', [
            'controller_name' => 'StyleController',
        ]);
    }

    /**
     * @Route("/insights", name="insights")
     */
    public function insights()
    {
        return $this->render('web/guide/insights.html.twig', [
            'controller_name' => 'StyleController',
        ]);
    }
}

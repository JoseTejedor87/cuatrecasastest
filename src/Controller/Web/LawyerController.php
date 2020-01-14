<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/lawyer", name="lawyer")
*/
class LawyerController extends WebController
{
    /**
     * @Route("/detail", name="detail")
     */
    public function detail()
    {
        return $this->render('web/lawyer/detail.html.twig', [
            'controller_name' => 'LawyerController',
        ]);
    }
    /**
     * @Route("/filter", name="filter")
     */
    public function filter()
    {
        return $this->render('web/lawyer/filter.html.twig', [
            'controller_name' => 'LawyerController',
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/services", name="services")
*/
class ServicesController extends AbstractController
{
    /**
     * @Route("/labourLaw", name="labourLaw")
     */
    public function labourLaw()
    {
        return $this->render('web/services/labourLaw.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    /**
     * @Route("/productDetail", name="productDetail")
     */
    public function productDetail()
    {
        return $this->render('web/services/productDetail.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }
}

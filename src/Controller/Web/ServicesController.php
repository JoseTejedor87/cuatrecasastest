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
     * @Route("/practiceAreas", name="practiceAreas")
     */
    public function practiceAreas()
    {
        return $this->render('web/services/practiceAreas.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    /**
     * @Route("/desks", name="desks")
     */
    public function desks()
    {
        return $this->render('web/services/desks.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    /**
     * @Route("/products", name="products")
     */
    public function products()
    {
        return $this->render('web/services/products.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

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
     * @Route("/labourLaw2", name="labourLaw2")
     */
    public function labourLaw2()
    {
        return $this->render('web/services/labourLaw2.html.twig', [
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

    /**
     * @Route("/fiscal", name="fiscal")
     */
    public function fiscal()
    {
        return $this->render('web/services/fiscal.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }
}

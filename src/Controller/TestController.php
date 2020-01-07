<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $instance = new \App\Entity\User();
        $entityManager->persist($instance);
        $entityManager->flush();

//        $instance = new \App\Entity\Lawyer();
//
//        $entityManager = $this->getDoctrine()->getManager();
//
//        $instance->translate('es')->setMetaTitle('Meta Title ES');
//        $instance->translate('en')->setMetaTitle('Meta Title EN');
//        $instance->translate('fr')->setMetaTitle('Meta Title FR');
//
//        // tell Doctrine you want to (eventually) save the instance (no queries yet)
//        $entityManager->persist($instance);
//
//        // In order to persist new translations, call mergeNewTranslations method, before flush
//        $instance->mergeNewTranslations();
//
//        // actually executes the queries (i.e. the INSERT query)
//        $entityManager->flush();
//
//        $instance = $entityManager->getRepository(\App\Entity\Lawyer::class)
//            ->find(14);
//
//        $t = $instance->translate('en')->getMetaTitle();

        return $this->render('subitem/index.html.twig', [
            'controller_name' => 'SubitemController',
        ]);
    }
}

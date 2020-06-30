<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\PublicationRepository;



use App\Controller\Web\WebController;



class KnowledgeController extends WebController
{

    public function insights()
    {
        return $this->render('web/knowledge/insights.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    public function featured()
    {
        return $this->render('web/knowledge/featured.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }


    public function productDetailKnowledge()
    {
        return $this->render('web/knowledge/productDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    // TEMPORAL >>> BORRAR
    public function filter()
    {
        return $this->render('web/knowledge/filter.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }




}

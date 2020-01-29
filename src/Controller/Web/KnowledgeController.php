<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/knowledge", name="knowledge")
*/
class KnowledgeController extends AbstractController
{
    /**
     * @Route("/insights", name="insights")
     */
    public function insights()
    {
        return $this->render('web/knowledge/insights.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    /**
     * @Route("/eventDetail", name="eventDetail")
     */
    public function eventDetail()
    {
        return $this->render('web/knowledge/eventDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }
}

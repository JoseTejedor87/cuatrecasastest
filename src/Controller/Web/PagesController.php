<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\WebController;


use App\Repository\PageRepository;

class PagesController extends WebController
{

    public function detail(Request $request, PageRepository $PageRepository)
    {

        $page = $PageRepository->getInstanceByRequest($request);

        $urlTemplate = 'empty';
        if (null !== $page->getCustomTemplate() && $page->getCustomTemplate() != '')
        {
            $urlTemplate = 'custom/'.$page->getCustomTemplate();
        }

        return $this->render('web/pages/'.$urlTemplate.'.html.twig', [
            'page' => $page,
            'controller_name' => 'PageController'
        ]);
    }

}

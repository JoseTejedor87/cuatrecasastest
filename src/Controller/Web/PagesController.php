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

        return $this->render('web/regions/'.$page->getCustomTemplate().'.html.twig', [
            'page' => $page,
            'controller_name' => 'PageController'
        ]);
    }

}

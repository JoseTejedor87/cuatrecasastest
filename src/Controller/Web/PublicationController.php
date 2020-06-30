<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Controller\Web\WebController;
use App\Repository\PublicationRepository;


class PublicationController extends WebController
{
    public function detail(Request $request, PublicationRepository $PublicationRepository)
    {
        $publication = $PublicationRepository->getInstanceByRequest($request);
        return $this->render('web/knowledge/articleDetail.html.twig', [
            'publication' => $publication,
        ]);
    }
}

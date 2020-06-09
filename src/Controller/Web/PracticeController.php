<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

class PracticeController extends WebController
{
    public function index(Request $request, PracticeRepository $practiceRepository)
    {
        $practices = $practiceRepository->createPublishedQueryBuilder('p')
            ->andwhere('p.highlighted = true')
            ->getQuery()
            ->getResult();

        return $this->render('web/practices/index.html.twig', [
            'practices' => $practices,
        ]);
    }

    public function detail(Request $request, PracticeRepository $practiceRepository, ActivityTranslationRepository $activityTranslationRepository)
    {
        $practice = $practiceRepository->getInstanceByRequest($request);

        return $this->render('web/practices/detail.html.twig', [
            'practice' => $practice,
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Web\WebController;
use App\Repository\OfficeRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use App\Controller\Web\NavigationService;


class OfficeController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    public function detail(Request $request, OfficeRepository $officeRepository, NavigationService $navigation)
    {
        //  $VarName = $officeRepository->findAll();
        //dd($request);

        $office = $officeRepository->getInstanceByRequest($request);
        
        return $this->render('web/shared/office-detail.html.twig', [
            'office' => ''
        ]);
    }


    public function index(Request $request,OfficeRepository $officeRepository, NavigationService $navigation)
    {
        return $officeRepository->getInstanceByRequest($request);
    }

}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Controller\Web\WebController;


class ProductsController extends WebController
{
    public function index(Request $request, ProductRepository $productRepository)
    {
        $products = $productRepository->createPublishedQueryBuilder('p')
        ->getQuery()
        ->getResult();

        return $this->render('web/products/index.html.twig', [
            'products' => $products,
        ]);
    }
    public function detail(Request $request, productRepository $productRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository)
    {
        $awards = $awardRepository->getAll();
        $product = $productRepository->getInstanceByRequest($request);

        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$product]
        );

        return $this->render('web/products/detail.html.twig', [
            'product' => $product,
            'relatedCaseStudies' => $relatedCaseStudies,
            'awards' => $awards
        ]);
    }

}

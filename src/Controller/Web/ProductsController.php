<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;


class ProductsController extends WebController
{
    public function index(Request $request, ProductRepository $productRepository, PublicationRepository $publicationRepository)
    {
        $products = $productRepository->createPublishedQueryBuilder('p')
        ->getQuery()
        ->getResult();
        $relatedPublications = $publicationRepository->findByActivities([]);
        return $this->render('web/products/index.html.twig', [
            'products' => $products,
            'relatedPublications' => $relatedPublications
        ]);
    }
    public function detail(Request $request, productRepository $productRepository, CaseStudyRepository $caseStudyRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository)
    {
        $awards = $awardRepository->getAll();
        $product = $productRepository->getInstanceByRequest($request);
        $key_contacts = $product->getKeyContacts();
        $relatedPublications = $publicationRepository->findByActivities([$product]);
        $relatedCaseStudies = $caseStudyRepository->findByActivities(
            [$product]
        );

        $awardsFiltered = [];
        foreach ($awards as $award)
        {
            foreach($award->getActivities() as $activity){
                if ( $activity instanceof \App\Entity\Products)
                {
                    array_push($awardsFiltered,$award);
                    break;
                }
            }
        }

        return $this->render('web/products/detail.html.twig', [
            'product' => $product,
            'key_contacts' => $key_contacts,
            'relatedCaseStudies' => $relatedCaseStudies,
            'relatedPublications' => $relatedPublications,
            'awards' => $awardsFiltered
        ]);
    }

}

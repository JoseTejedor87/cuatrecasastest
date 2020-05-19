<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PracticeRepository;
use App\Repository\DeskRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

/**
* @Route("/{idioma}/services", name="services")
*/
class ServicesController extends WebController
{
    /**
     * @Route("/practiceAreas", name="practiceAreas")
     */
    public function practiceAreas(Request $request,PracticeRepository $PracticeRepository)
    {
        $practices = $PracticeRepository->findAll();
        $this->isThisLocale($request, $request->attributes->get('idioma'));

        return $this->render('web/services/practiceAreas.html.twig', [
            'controller_name' => 'ServicesController',
            'practices' => $practices,
        ]);
    }

    /**
     * @Route("/desks", name="desks")
     */
    public function desks(Request $request,DeskRepository $DeskRepository)
    {
        $desks = $DeskRepository->findAll();
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/services/desks.html.twig', [
            'controller_name' => 'ServicesController',
            'desks' => $desks,
        ]);
    }

    /**
     * @Route("/products", name="products")
     */
    public function products()
    {
        $sectors = $sectorRepository->findAll();
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/services/products.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    /**
     * @Route("/serviceDetail/{slug}", name="serviceDetail")
     */
    public function labourLaw(Request $request,PracticeRepository $practiceRepository, DeskRepository $deskRepository, ActivityTranslationRepository $activityTranslationRepository)
    {
        $activityTranslation = $activityTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $service = $practiceRepository->findOneBy(['id' => $activityTranslation->getTranslatable()->getId()]);
        if(!$service){
            $service = $deskRepository->findOneBy(['id' => $activityTranslation->getTranslatable()->getId()]);
        }
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/services/labourLaw.html.twig', [
            'controller_name' => 'ServicesController',
            'service' => $service,
        ]);
    }

    /**
     * @Route("/practiceAreasDetail/{slug}", name="practiceAreasDetail")
     */
    public function labourLaw2(Request $request,PracticeRepository $practiceRepository, ActivityTranslationRepository $activityTranslationRepository)
    {
        $activityTranslation = $activityTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $practice = $practiceRepository->findOneBy(['id' => $activityTranslation->getTranslatable()->getId()]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));

        return $this->render('web/services/labourLaw2.html.twig', [
            'controller_name' => 'ServicesController',
            'practice' => $practice,
        ]);
    }

    /**
     * @Route("/productDetail", name="productDetail")
     */
    public function productDetail()
    {
        return $this->render('web/services/productDetail.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }

    /**
     * @Route("/fiscal", name="fiscal")
     */
    public function fiscal()
    {
        return $this->render('web/services/fiscal.html.twig', [
            'controller_name' => 'ServicesController',
        ]);
    }
}

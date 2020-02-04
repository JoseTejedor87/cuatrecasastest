<?php

namespace App\Controller\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


use App\Entity\Lawyer;
use App\Repository\LawyerRepository;
use App\Repository\LawyerTranslationRepository;

/**
* @Route("/{idioma}/lawyer", name="lawyer", methods={"GET"})
*/
class LawyerController extends AbstractController
{
    /**
     * @Route("/detail/{slug}", name="detail", methods={"GET"})
     */
    public function detail(Request $request, LawyerRepository $lawyerRepository, LawyerTranslationRepository $LawyerTranslationRepository)
    {
       
        $lawyer = $lawyerRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $locale = $request->attributes->get('idioma');
        return $this->render('web/lawyer/detail.html.twig', [
            'controller_name' => 'LawyerController',
            'lawyer' => $lawyer,
            'locale' => $locale,

        ]);
    }

    /**
     * @Route("/filter", name="filter")
     */
    public function filter()
    {
        return $this->render('web/lawyer/filter.html.twig', [
            'controller_name' => 'LawyerController',
        ]);
    }
}

<?php

namespace App\Controller\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Web\WebController;

use App\Entity\Lawyer;
use App\Repository\LawyerRepository;

/**
* @Route("/{idioma}/lawyer", name="lawyer", methods={"GET"})
*/
class LawyerController extends WebController
{
    /**
     * @Route("/detail/{slug}", name="detail", methods={"GET"})
     */
    public function detail(Request $request, LawyerRepository $lawyerRepository)
    {
       
        $lawyer = $lawyerRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        
        $activities = $lawyer->getActivities();
        //dd($activities);
        // foreach ($activities as $key => $value) {
        //     dd($value->translate('es')->getTitle());
        // }
        return $this->render('web/lawyer/detail.html.twig', [
            'controller_name' => 'LawyerController',
            'lawyer' => $lawyer,

        ]);
    }

    /**
     * @Route("/filter", name="filter", methods={"GET"})
     */
    public function filter(Request $request, LawyerRepository $lawyerRepository)
    {
        $initial = $request->query->get('initial');
        if(!$initial){
            $initial = $request->query->get('search');
        }
        if(!$initial){
            $lawyers = $lawyerRepository->findAll();
        }else{
            $lawyers = $lawyerRepository->findBy(['surname'=> 'p%']); 
            // createQuery("SELECT TOP * FROM Lawyer where surname like 'p%'");
            $query = $lawyerRepository->createQueryBuilder('l')
               ->where('l.surname LIKE :surname')
               ->setParameter('surname', $initial.'%')
               ->getQuery();
               $lawyers = $query->getResult();
        }
        $countLawyers = count($lawyers);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        //dd($lawyers);
        return $this->render('web/lawyer/filter.html.twig', [
            'controller_name' => 'LawyerController',
            'lawyers' => $lawyers,
            'countLawyers' => $countLawyers,
        ]);
    }
}

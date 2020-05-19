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
        //dd($lawyer->translate('es'));
        //  $Person = $lawyer->getPerson();
        //  $Events = $Person->getEvents();
        //  var_dump($Events);
        //  die('');
        // $Office = $lawyer->getOffice();
        // var_dump($Person);
        // $descrip = $Office->translate('es')->getDescriptions();
        // var_dump($Office);
        // $Officeid = $Office->getId();
        // var_dump($Office->translate('es')->getDescriptions());
        // die();
        // dd(var_dump($lawyer));
        // foreach ($Events as $key => $value) {
        //     dd($value->translate('es')->getTitle());
        // }
        // die();
        // var_dump($lawyer);
        // dd($lawyer);
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
        $page = $request->query->get('page');
        if(!isset($page))
        $page = 1;
        $limit = 18;
        if(!$initial ){
        $initial = $request->query->get('initial');

        }
        if($initial ){
            //$lawyers = $lawyerRepository->findBy(['surname'=> 'p%']);
            // createQuery("SELECT TOP * FROM Lawyer where surname like 'p%'");
            $query = $lawyerRepository->createQueryBuilder('l')
               ->where('l.surname LIKE :surname')
               ->setParameter('surname', $initial .'%')
               ->setFirstResult($limit * ($page - 1))
               ->setMaxResults($limit)
               ->getQuery();
            $lawyers = $query->getResult();
            $query = $lawyerRepository->createQueryBuilder('l')
               ->where('l.surname LIKE :surname')
               ->setParameter('surname', $initial .'%')
               ->getQuery();
               $querySql = $query->getSQL();
               if($lawyers){
                $countLawyers = count($query->getResult());
                $pagesTotal = $countLawyers/$limit;
                if(is_float($pagesTotal) && $pagesTotal>=1){
                    $pagesTotal = intval($pagesTotal + 1);
                }
               }
        }


        // dd($_SERVER['REQUEST_URI']);
        return $this->render('web/lawyer/filter.html.twig', [
            'controller_name' => 'LawyerController',
            'lawyers' => isset($lawyers) ? $lawyers : '',
            'countLawyers' => isset($countLawyers) ? $countLawyers : '',
            'page' => isset($page) ? $page : '',
            'pagesTotal' => isset($pagesTotal) ? $pagesTotal : '',
        ]);
    }
}

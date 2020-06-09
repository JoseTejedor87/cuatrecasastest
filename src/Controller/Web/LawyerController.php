<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Web\WebController;
use App\Entity\Lawyer;
use App\Repository\LawyerRepository;

class LawyerController extends WebController
{
    public function detail(Request $request, LawyerRepository $lawyerRepository)
    {
        if ($lawyer = $lawyerRepository->findOneBy(['slug' => $request->attributes->get('slug')])) {
            return $this->render('web/lawyer/detail.html.twig', [
                'controller_name' => 'LawyerController',
                'lawyer' => $lawyer,

            ]);
        }
    }

    public function index(Request $request, LawyerRepository $lawyerRepository)
    {
        $initial = $request->query->get('initial');
        $page = $request->query->get('page') ?: 1;
        $limit = 18;

        if ($initial) {
            $query = $lawyerRepository->createPublishedQueryBuilder('l')
               ->andwhere('l.surname LIKE :surname')
               ->setParameter('surname', $initial .'%');

            $lawyers = $query->getQuery()->getResult();

            if ($lawyers) {
                $query
                    ->setFirstResult($limit * ($page - 1))
                    ->setMaxResults($limit);
                $countLawyers = count($query->getQuery()->getResult());
                $pagesTotal = $countLawyers/$limit;
                if (is_float($pagesTotal) && $pagesTotal >= 1) {
                    $pagesTotal = intval($pagesTotal + 1);
                }
            }
        }

        if ($request->isXMLHttpRequest()) {
            $lawyerA = array();
            if ($lawyers) {
                foreach ($lawyers as $key => $lawyer) {
                    $lawyerA[$key] = array( 'FullName' => $lawyer->getName(). ' ' .  $lawyer->getSurname(), 'LawyerType' => $lawyer->getLawyerType(), 'Slug' => $lawyer->getSlug());
                    $activities = "";
                    foreach ($lawyer->getActivities() as $activity) {
                        $activities = $activities. ' ' . $activity->translate('es')->getTitle();
                    }
                    $lawyerA[$key]['activities'] = $activities;
                    $lawyerA[$key]['office'] = $lawyer->getOffice()->getCity();
                }
            }
            return new JsonResponse(array('lawyers' => $lawyerA,'countLawyers' => $countLawyers,'pagesTotal' => $pagesTotal ,'page' => $page,'initial' => $initial));
        } else {
            return $this->render('web/lawyer/index.html.twig', [
                'controller_name' => 'LawyerController',
                'lawyers' => isset($lawyers) ? $lawyers : '',
                'countLawyers' => isset($countLawyers) ? $countLawyers : '',
                'page' => isset($page) ? $page : '',
                'pagesTotal' => isset($pagesTotal) ? $pagesTotal : '',
            ]);
        }
    }
}

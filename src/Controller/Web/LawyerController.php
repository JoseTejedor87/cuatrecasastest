<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Controller\Web\WebController;
use App\Entity\Lawyer;
use App\Repository\LawyerRepository;
use App\Repository\SectorRepository;
use App\Repository\PracticeRepository;
use App\Repository\OfficeRepository;

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

    public function filter(Request $request, LawyerRepository $lawyerRepository,SectorRepository $sectorRepository,PracticeRepository $PracticeRepository,OfficeRepository $OfficeRepository)
    {
        $practices = $PracticeRepository->findAll();
        $sectors = $sectorRepository->findAll();
        $offices = $OfficeRepository->findAll();

        $initial = $request->query->get('initial');
        $page = $request->query->get('page') ?: 1;
        $textSearch = $request->query->get('textSearch');
        $services = $request->query->get('services');
        $sector = $request->query->get('sector');
        $office = $request->query->get('office');


        $limit = 18;
        if ($initial || $office || $sector || $services || $textSearch) {
            $url= "";
            $query = $lawyerRepository->createQueryBuilder('l');
                if($services){
                    $query = $query->innerJoin('l.activities', 'a')
                    ->andWhere('a.id = :activity')
                    ->setParameter('activity', $services);
                    if($url == ""){
                        $url= "?services=".$services;
                    }else{
                        $url= $url . "&services=".$services;
                    }
                }
                if($sector){
                    $query = $query->innerJoin('l.activities', 's')
                    ->andWhere('s.id = :sector')
                    ->setParameter('sector', $sector);
                    if($url == ""){
                        $url= "?sector=".$sector;
                    }else{
                        $url= $url . "&sector=".$sector;
                    }
                }
                if($office){
                    $query = $query->innerJoin('l.office', 'o')
                    ->andWhere('l.office = :city')
                    ->setParameter('city', $office);
                    if($url == ""){
                        $url= "?office=".$office;
                    }else{
                        $url= $url . "&office=".$office;
                    }
                }
                if($textSearch){
                    $query = $query->andWhere("CONCAT( l.name,  ' ', l.surname ) LIKE :textSearch")
                    ->setParameter('textSearch', '%'.$textSearch .'%');
                    if($url == ""){
                        $url= "?textSearch=".$textSearch;
                    }else{
                        $url= $url . "&textSearch=".$textSearch;
                    }
                }
                if($initial){
                    $query = $query->andWhere('l.surname LIKE :surname')
                    ->setParameter('surname', $initial .'%');
                    if($url == ""){
                        $url= "?initial=".$initial;
                    }else{
                        $url= $url . "&initial=".$initial;
                    }
                }
            $countLawyers = count($query->getQuery()->getResult());
            $query = $query->setFirstResult($limit * ($page - 1))
                ->setMaxResults($limit)
                ->getQuery();
            $lawyers = $query->getResult();
            if($lawyers){
                $pagesTotal = $countLawyers/$limit;
                if(is_float($pagesTotal)){
                    $pagesTotal = intval($pagesTotal + 1);
                }
            }
        }
        if ($request->isXMLHttpRequest()) {
            
            $lawyerA = array();
            if (isset($lawyers)) {
                foreach ($lawyers as $key => $lawyer) {
                    $lawyerA[$key] = array( 'FullName' => $lawyer->getName(). ' ' .  $lawyer->getSurname(), 'LawyerType' => $lawyer->getLawyerType(), 'Slug' => $lawyer->getSlug());
                    $activities = "";
                    foreach ($lawyer->getActivities() as $activity) {
                        $activities = $activities. ' ' . $activity->translate('es')->getTitle();
                    }
                    $lawyerA[$key]['activities'] = $activities;
                    $lawyerA[$key]['office'] = $lawyer->getOffice() ? $lawyer->getOffice()->getCity() : '';
                }
            }
            $json = array(
                'lawyers' => $lawyerA,'countLawyers' => isset($countLawyers) ? $countLawyers : 0,'pagesTotal' => isset($pagesTotal) ? $pagesTotal : 0 ,'page' => isset($page) ? $page : 0
            );
            
            if($initial){
                $json['initial']= $initial;

            }
            if($office){
                $json['office'] = $office;
            }
            if($textSearch){
                $json['textSearch'] = $textSearch;
            }
            if($services){
                $json['services'] = $services;
            }
            if($sector){
                $json['sector'] = $sector;
            }
            return new JsonResponse($json);
        } else {
            return $this->render('web/lawyer/index.html.twig', [
                'controller_name' => 'LawyerController',
                'lawyers' => isset($lawyers) ? $lawyers : '',
                'countLawyers' => isset($countLawyers) ? $countLawyers : '',
                'page' => isset($page) ? $page : '',
                'pagesTotal' => isset($pagesTotal) ? $pagesTotal : '',
                'sectors' => $sectors,
                'practices' => $practices,
                'offices' => $offices,
                'url' => isset($url) ? $url : '',
            ]);
        }
    }
}

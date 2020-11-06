<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use App\Repository\RegionRepository;
use App\Repository\AwardRepository;
use App\Repository\DeskRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\OfficeRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;
use App\Repository\GeneralBlockRepository;

class LocationController extends WebController
{
    public function index(Request $request, RegionRepository $RegionRepository,DeskRepository $DeskRepository,OfficeRepository $OfficeRepository, NavigationService $navigation)
    {
     
        $regions = $RegionRepository->findAll();
        $desks = $DeskRepository->findAll();
        $offices = $OfficeRepository->findAll();
        $officeA = array();
        $officeATest = array();
        $officesLat = $OfficeRepository->createQueryBuilder('o')
        ->where("o.lat != ''")
        //  ->orderBy('o.country', 'DESC')  PAra ordenarlos hay que hacerlo contra OfficeTranstable.country
        ->getQuery()->getResult();
        foreach ($officesLat as $key => $office) {
            array_push($officeA, '<h5 id="'.$office->getId().'"><a href="'.$this->container->get('router')->generate('offices_detail', array('slug' => $office->getSlug())).'">'.$office->translate($navigation->getLanguage())->getCountry().'</a></h5><h6>'.$office->translate($navigation->getLanguage())->getCity().'</h6><p>'.$office->getAddress().'</p>');
            array_push($officeATest, [ "lat" => floatval($office->getLat()),"lng" =>  floatval($office->getLng())]);
        }
        return $this->render('web/location/index.html.twig', array(
            'regiones' => $regions,
            'officesMapa' => isset($officeATest) ? json_encode($officeATest)  : '',
            'officesMapaLabel' => isset($officeA) ? json_encode($officeA)  : '',
            'officesLat' => isset($officesLat) ? $officesLat  : '',
            'offices' => isset($offices) ? $offices  : '',
            'desks' => isset($desks) ? $desks  : '',
            'caseStudies' => isset($cases) ? $cases  : '',
        ));
    }


    public function detail(Request $request, RegionRepository $RegionRepository, AwardRepository $awardRepository, PublicationRepository $publicationRepository,OfficeRepository $OfficeRepository, NavigationService $navigation)
    {
        $officeA = array();
        $officeATest = array();
        // $awards = $awardRepository->getAll();
        $regions = $RegionRepository->findAll();
        $regiond = $RegionRepository->getInstanceByRequest($request);
        $relatedPublications = $publicationRepository->findByActivities('');
        $officesLat = $OfficeRepository->createQueryBuilder('o')
        ->innerJoin('o.region', 'ro')
        ->andWhere('ro.id = :region')
        ->setParameter('region',  $regiond->getId())
        ->andWhere("o.lat != ''")
        ->getQuery()->getResult();
        
        
        foreach ($officesLat as $key => $office) {
            array_push($officeA, '<h5 id="'.$office->getId().'"><a href="'.$this->container->get('router')->generate('offices_detail', array('slug' => $office->getSlug())).'">'.$office->translate($navigation->getLanguage())->getCountry().'</a></h5><h6>'.$office->translate($navigation->getLanguage())->getCity().'</h6><p>'.$office->getAddress().'</p>');
            array_push($officeATest, [ "lat" => floatval($office->getLat()),"lng" =>  floatval($office->getLng())]);
            if(floatval($office->getLat())>0){
                $centerMap=['lat'=>40.4165, 'lng'=>-3.70256 ,'zoom'=>6.7];
            }else{
                $centerMap=['lat'=>-15.7801, 'lng'=>-47.9292 ,'zoom'=>4];
            }
        }
        return $this->render('web/location/detail.html.twig', array(
            'regiond' => $regiond,
            'regiones' => $regions,
            'relatedPublications' => $relatedPublications,
            'officesMapa' => isset($officeATest) ? json_encode($officeATest)  : '',
            'officesMapaLabel' => isset($officeA) ? json_encode($officeA)  : '',
            'officesLat' => isset($officesLat) ? $officesLat  : '',
            'centerMap' => isset($centerMap) ? $centerMap  : '',
        ));
    }
    public function indexOther(Request $request, RegionRepository $RegionRepository, PublicationRepository $publicationRepository)
    {
        $relatedPublications = $publicationRepository->findByActivities('');
        $regions = $RegionRepository->findAll();
        return $this->render('web/location/others.html.twig', [
            'regiones' => $regions,
            'relatedPublications' => $relatedPublications,
        ]);
    }


    public function detailOther(Request $request, RegionRepository $RegionRepository, AwardRepository $awardRepository, CaseStudyRepository $CaseStudyRepository, PublicationRepository $publicationRepository)
    {
        
        $regiond = $RegionRepository->getInstanceByRequest($request);
        $relatedPublications = $publicationRepository->findByActivities('');
        $offices = $regiond->getOffice();
        $lawyers = array();
        $lawyersid = array();
        foreach ( $offices as $key => $office) {
            foreach ($office->getLawyer() as $key => $value) {
                if( $value->getPublished()){
                    array_push( $lawyers, $value);
                    array_push( $lawyersid, $value->getId());
                }
            }
        }
        $relatedCaseStudies = $CaseStudyRepository->findByLawyersId($lawyers);
        $key_contacts =  $lawyers;
        return $this->render('web/location/detailOthers.html.twig', [
            'regiond' => $regiond,
            'relatedPublications' => $relatedPublications,
            'key_contacts' => $key_contacts,
            'relatedCaseStudies' => $relatedCaseStudies,
        ]);
    }
}

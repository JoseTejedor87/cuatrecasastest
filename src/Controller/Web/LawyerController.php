<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

use App\Controller\Web\WebController;
use App\Entity\Lawyer;
use App\Repository\LawyerRepository;
use App\Repository\SectorRepository;
use App\Repository\PracticeRepository;
use App\Repository\OfficeRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\InsightRepository;
use App\Repository\TrainingRepository;
use App\Repository\PublicationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

// for download VSCARD
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Repository\GeneralBlockRepository;

use App\Controller\Web\NavigationService;

class LawyerController extends WebController
{
    protected $imagineCacheManager;

    public function __construct(CacheManager $imagineCacheManager)
    {
        $this->imagineCacheManager = $imagineCacheManager;
    }

    protected function getPhotoPathByFilter($lawyer, $filter, $navigation)
    {
        $photo = $lawyer->getPhoto();
        if ($photo->isPublished($navigation->getLanguage(), $navigation->getRegion())) {
            if ($photo->getType() == "publication_main_photo") {
                $photo = $this->imagineCacheManager->getBrowserPath(
                    '/resources/' . $photo->getFileName(),
                    $filter
                );
                return $photo;
            }
        }
    }

    public function detail(Request $request, LawyerRepository $lawyerRepository, CaseStudyRepository $caseStudyRepository, NavigationService $navigation)
    {
        $lawyer = $lawyerRepository->getInstanceByRequest($request);
        $contextualBlocks['cases']  = $caseStudyRepository->findByLawyer($lawyer);
        return $this->render('web/lawyer/detail.html.twig', [
            'lawyer' => $lawyer,
            'contextualBlocks' => $contextualBlocks,
        ]);
    }


    public function index(
        Request $request,
        TranslatorInterface $translator,
        LawyerRepository $lawyerRepository,
        SectorRepository $sectorRepository,
        PracticeRepository $PracticeRepository,
        InsightRepository $insightRepository,
        OfficeRepository $OfficeRepository,
        PublicationRepository $publicationRepository,
        GeneralBlockRepository $generalBlockRepository,
        NavigationService $navigation
    ) {
        $blockCareer = $generalBlockRepository->findOneBy(['blockName' => 'block_career']);
        $practices = $PracticeRepository->findAll();
        $sectors = $sectorRepository->findAll();
        $offices = $OfficeRepository->findBy(['published' => true]);
        $insightsPrior = $insightRepository->getInsightsPriorFor(['showLegalNoveltiesBlock' => true]);
        $insightsAll = $insightRepository->findBy(['showLegalNoveltiesBlock' => true], ['id' => 'DESC']);
        $totalInsights = [];
        foreach ($insightsPrior as $key => $item) {
            $totalInsights[$item->getId()] = $item;
        }
        foreach ($insightsAll as $key => $item) {
            if (!isset($totalInsights[$item->getId()])) {
                array_push($totalInsights, $item);
            }
        }


        $initial = $request->query->get('initial');
        $page = $request->query->get('page') ?: 1;
        $textSearch = $request->query->get('textSearch');
        $services = $request->query->get('services');
        $sector = $request->query->get('sector');
        $office = $request->query->get('office');
        $lawyerType = $request->query->get('lawyerType');


        $relatedPublications = $publicationRepository->findByActivities('');
        $limit = 18;
        if ($initial || $office || $sector || $services || $textSearch || $lawyerType) {
            $url= "";
            $query = $lawyerRepository->createPublishedQueryBuilder('l');
            if ($services) {
                $query = $query->innerJoin('l.activities', 'a')
                    ->andWhere('a.id = :activity')
                    ->setParameter('activity', $services);
                $query = $query->innerJoin('l.secondaryActivities', 'ss')
                    ->orWhere('ss.id = :activity')
                    ->setParameter('activity', $services);
                if ($url == "") {
                    $url= "?services=".$services;
                } else {
                    $url= $url . "&services=".$services;
                }
            }
            if ($lawyerType) {
                $query = $query->andWhere('l.lawyerType = :lawyerType')
                    ->setParameter('lawyerType', $lawyerType);
                if ($url == "") {
                    $url= "?lawyerType=".$lawyerType;
                } else {
                    $url= $url . "&lawyerType=".$lawyerType;
                }
            }
            if ($sector) {
                $query = $query->innerJoin('l.activities', 's')
                    ->andWhere('s.id = :sector')
                    ->setParameter('sector', $sector);
                $query = $query->innerJoin('l.secondaryActivities', 'ses')
                    ->orWhere('ses.id = :sector')
                    ->setParameter('sector', $sector);
                if ($url == "") {
                    $url= "?sector=".$sector;
                } else {
                    $url= $url . "&sector=".$sector;
                }
            }
            if ($office) {
                $query = $query->innerJoin('l.office', 'of')
                    ->andWhere('l.office = :city')
                    ->setParameter('city', $office);
                if ($url == "") {
                    $url= "?office=".$office;
                } else {
                    $url= $url . "&office=".$office;
                }
            }
            if ($textSearch) {
                $query = $query->andWhere("CONCAT( l.name,  ' ', l.surname ) LIKE :textSearch")
                    ->setParameter('textSearch', '%'.$textSearch .'%');
                if ($url == "") {
                    $url= "?textSearch=".$textSearch;
                } else {
                    $url= $url . "&textSearch=".$textSearch;
                }
            }
            if ($initial) {
                $query = $query->andWhere('l.surname LIKE :surname')
                    ->setParameter('surname', $initial .'%');
                if ($url == "") {
                    $url= "?initial=".$initial;
                } else {
                    $url= $url . "&initial=".$initial;
                }
            }
            $query->orderBy('l.lawyerType, l.surname', 'DESC');

            $qb = clone $query;
            $lawyersPrior = $lawyerRepository->priorBuilderClause($qb, 'l.office')->getQuery()->getResult();

            $totalLawyers = [];
            foreach ($lawyersPrior as $key => $item) {
                $totalLawyers[$item->getId()] = $item;
            }

            $lawyersAll = $query->getQuery()->getResult();

            foreach ($lawyersAll as $key => $item) {
                if (!isset($totalLawyers[$item->getId()])) {
                    array_push($totalLawyers, $item);
                }
            }


            $lawyers = array_slice($totalLawyers, ($limit * ($page - 1)), $limit);

            if ($totalLawyers) {
                $countLawyers =count($totalLawyers);
                $pagesTotal = $countLawyers/$limit;

                if (is_float($pagesTotal)) {
                    $pagesTotal = intval($pagesTotal + 1);
                }
            }

            /*
            $query = $query->setFirstResult($limit * ($page - 1))
                ->setMaxResults($limit)
                ->getQuery();
            $lawyers = $query->getResult();
            if ($lawyers) {
                $pagesTotal = $countLawyers/$limit;
                if (is_float($pagesTotal)) {
                    $pagesTotal = intval($pagesTotal + 1);
                }
            }*/
        }
        if ($request->isXMLHttpRequest()) {
            $lawyerA = array();
            if (isset($lawyers)) {
                foreach ($lawyers as $key => $lawyer) {
                    $url =  $this->container->get('router')->generate('lawyers_detail', array('slug' => $lawyer->getSlug()));
                    $lawyerA[$key] = array( 'FullName' => $lawyer->getName(). ' ' .  $lawyer->getSurname(), 'LawyerType' => $translator->trans('sections.lawyers.lawyerCategoryTypes.'.$lawyer->getLawyerType()), 'Slug' => $url);
                    $activities = "";
                    foreach ($lawyer->getActivities() as $activity) {
                        $activities = $activities. ' ' . $activity->translate('es')->getTitle();
                    }
                    $lawyerA[$key]['activities'] = $activities;
                    $lawyerA[$key]['office'] = $lawyer->getOffice() ? $lawyer->getOffice()->translate($navigation->getLanguage())->getCity() : '';
                    $lawyerA[$key]['photo'] = $this->getPhotoPathByFilter($lawyer, 'lawyers_grid', $navigation);
                }
            }
            $json = array(
                'lawyers' => $lawyerA,'countLawyers' => isset($countLawyers) ? $countLawyers : 0,'pagesTotal' => isset($pagesTotal) ? $pagesTotal : 0 ,'page' => isset($page) ? $page : 0
            );

            if ($initial) {
                $json['initial']= $initial;
            }
            if ($office) {
                $json['office'] = $office;
            }
            if ($textSearch) {
                $json['textSearch'] = $textSearch;
            }
            if ($services) {
                $json['services'] = $services;
            }

            if ($sector) {
                $json['sector'] = $sector;
            }

            if ($lawyerType) {
                $json['lawyerType'] = $lawyerType;
            }

            return new JsonResponse($json);
        } else {
            $sectors = $sectorRepository->getSectorsIfLawyers();
            $offices = $OfficeRepository->getOfficesIfLawyers();
            $practices = $PracticeRepository->getPracticesIfLawyers();

            return $this->render('web/lawyer/index.html.twig', [
                'controller_name' => 'LawyerController',
                'lawyers' => isset($lawyers) ? $lawyers : '',
                'countLawyers' => isset($countLawyers) ? $countLawyers : '',
                'page' => isset($page) ? $page : '',
                'pagesTotal' => isset($pagesTotal) ? $pagesTotal : '',
                'sectors' => $sectors,
                'practices' => $practices,
                'offices' => $offices,
                'relatedPublications' => $relatedPublications,
                'url' => isset($url) ? $url : '',
                'career' => $blockCareer,
                'insights' => $totalInsights
            ]);
        }
    }



    public function download(Request $request, LawyerRepository $lawyerRepository, OfficeRepository $officeRepository)
    {
        $lawyer = $lawyerRepository->findOneBy(['id' => $request->attributes->get('id')]);

        $filesystem = new Filesystem();

        $officeData = '';
        if ($lawyer->getOffice() != null) {
            $office = $officeRepository->findOneBy(['id' => $lawyer->getOffice()->getId()]);
            $officeData .= 'ADR;TYPE=WORK,PREF:;;'.$office->getAddress().';'.$office->translate($navigation->getLanguage())->getCity().';'.$office->getCp().';'.$office->translate($navigation->getLanguage())->getCountry()."\n";
            $officeData .= 'LABEL;TYPE=WORK,PREF:'.$office->getAddress().';'.$office->translate($navigation->getLanguage())->getCity().';'.$office->getCp().';'.$office->translate($navigation->getLanguage())->getCountry()."\n";
            $officeData .= 'TEL;TYPE=WORK,VOICE:'.$office->getPhone()."\n";
        }

        header('Content-Type: text/x-vcard;CHARSET=windows-1252');
        header('Content-Disposition: inline; filename= "'.$lawyer->getFullName().'.vcf'.'"');



        $img = file_get_contents($this->getPhotoPathByFilter($lawyer, 'lawyers_grid', $navigation));
        $dataImage64 = base64_encode($img);

        $dataString =   'BEGIN:VCARD'."\n".
                        'VERSION:3.0'."\n".
                        'N:'.$lawyer->getSurname().';'.$lawyer->getName()."\n".
                        'FN:'.$lawyer->getFullName()."\n".
                        'ORG:Cuatrocasas'."\n".
                        'TITLE:'.$lawyer->getLawyerType()."\n".
                        //'PHOTO;VALUE=URI;TYPE=GIF:http://'.$lawyer->getPhoto()->getFile()."\n".
                        //'PHOTO;VALUE=URI;TYPE=JPG:'.$this->getPhotoPathByFilter($lawyer, 'lawyers_grid',$navigation)."\n".
                        'PHOTO;TYPE=JPEG;ENCODING=BASE64:'.$dataImage64."\n".
                        'TEL;TYPE=HOME,VOICE:'.$lawyer->getPhone()."\n".
                        'TEL;TYPE=FAX,VOICE:'.$lawyer->getFax()."\n";

        if ($officeData != '') {
            $dataString .= $officeData;
        }
        $dataString .=  'EMAIL:'.$lawyer->getEmail()."\n".
                        //'REV:'.$lawyer->getUpdateAt().
                        'END:VCARD';
        $string_encoded = iconv(mb_detect_encoding($dataString), 'Windows-1252//TRANSLIT', $dataString);

        $filesystem->dumpFile('vcard_'.$lawyer->getFullName().'.vcf', $string_encoded);
        $file = new File('vcard_'.$lawyer->getFullName().'.vcf');

        return $this->file($file);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\PublicationRepository;
use App\Controller\Web\WebController;
use App\Controller\SOAPContactsClientController;
use App\Controller\Web\NavigationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Repository\OfficeRepository;
use App\Repository\ActivityRepository;

class EventController extends WebController
{
    private $soap;
    private $em;
    private $conn;

    public function __construct(ContainerInterface $container)
    {
        $this->soap  = new SOAPContactsClientController;
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->conn = $this->em->getConnection();
    }

    public function index(Request $request, EventRepository $EventRepository, NavigationService $navigation,PublicationRepository $publicationRepository, OfficeRepository $OfficeRepository,  ActivityRepository $ActivityRepository)
    {
  
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $activity = $request->query->get('activity');
        $office = $request->query->get('office');
        $relatedEvents = $EventRepository->findByActivities('');
        $relatedPublications = $publicationRepository->findByActivities('');
        $activities = $ActivityRepository->findAll();
        $offices = $OfficeRepository->findAll();
        // dd($relatedPublications);
        if( !$month ||  !$year){
            $fechaHoy = new \DateTime();
            if(!$month){
                $month = $fechaHoy->format('m');
            }
            if(!$year){
                $year = $fechaHoy->format('Y');
            }
        }

        $fecha = new \DateTime($year.'-'.$month.'-01');
        $lastday = date('t',strtotime($fecha->format('Y-m-d H:i:s')));
        $fechaFin = new \DateTime($year.'-'.$month.'-'.$lastday);
        $url= "";
        $query = $EventRepository->createPublishedQueryBuilder('e');
        if ($activity) {
            $query = $query->innerJoin('e.activities', 'a')
                ->andWhere('a.id = :activity')
                ->setParameter('activity', $activity);
            if ($url == "") {
                $url= "?activity=".$activity;
            } else {
                $url= $url . "&activity=".$activity;
            }
        }
        if ($office) {
            $query = $query->innerJoin('e.office', 'o')
                ->andWhere('e.office = :city')
                ->setParameter('city', $office);
            if ($url == "") {
                $url= "?office=".$office;
            } else {
                $url= $url . "&office=".$office;
            }
        }
        $query = $query->andWhere('e.startDate BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', $fecha->format('Y-m-d H:i:s') )
        ->setParameter('endDate', $fechaFin->format('Y-m-d H:i:s') );
        $queryPrior = clone $query;

        $eventsAll = $query->getQuery()->getResult();

        // ----------------
        $place = $navigation->getParams()->get('app.office_place')[$navigation->getRegion()];        
        $queryPrior->join('e.office', 'o')->andWhere('o.place = :place')->setParameter('place',  $place);
        // -------------------
        $eventsPrior = $queryPrior->getQuery()->getResult();


       // dd($eventsPrior);

        $events = [];
        foreach ($eventsPrior as $key => $item) {
            $item->setCapacity(1);
            $events[$item->getId()] = $item;
        }
  
        foreach ($eventsAll as $key => $item) {
            if (!isset($events[$item->getId()])){
                $item->setCapacity(0);
                array_push($events, $item);
            }
        }  

        // if($activity){
        //     $sql = "SELECT e FROM App:Event e inner JOIN event_activity a ON a.event_id = e.idWHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."' and a.activity_id=".$activity;
        // }else{
        //     $sql = "SELECT e FROM App:Event e WHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."'";
        // }
        // if($office){
        //     $sql = $sql . "AND e.office_id=".$office;
        // }
        // $events = $this->getDoctrine()
        //                 ->getManager()
        //                 ->createQuery($sql)
        //                 ->getResult();

        $eventsCalendar = array();
        foreach ($events as $key => $event) {
            if($event->translate('es')->getSlug()){
                $activities = "";
                foreach ($event->getActivities() as $keyActivity => $activity) {
                    $activities = $activities . $activity->translate('es')->getTitle();
                }

                $array = array(
                    "title" => $event->translate('es')->getTitle(),
                    "titleURL" => $this->container->get('router')->generate('events_detail', array('slug' => $event->translate('es')->getSlug())) ,
                    "start" => $event->getStartDate()->format('Y-m-d\TH:i:s.uP'),
                    "end" => $event->getEndDate()->format('Y-m-d\TH:i:s.uP'),
                    "sector" => $activities,
                    "place" => $event->translate('es')->getCustomAddress(),
                    "placeLink" => "",
                    "fullDate" => "",
                    "fullTime" => "",
                    "button" => "Inscribirme",
                    "speakersTitle" => "Ponentes",
                    "speakers" =>  array( )
                );
                foreach ($event->getPeople() as $keySpeaker => $speaker) {
                    if($speaker->getLawyer()){
                        $speakerName = $speaker->getLawyer()->getName() .' '. $speaker->getLawyer()->getSurname();
                    }else{
                        $speakerName =$speaker->getName() .' '. $speaker->getSurname();
                    }
                    $speaker = array(
                        "speaker_name" => $speakerName,
                        "speaker_url" => "",
                    );
                    array_push($array['speakers'],$speaker);
                }
                array_push($eventsCalendar,$array);
            }
        }
        return $this->render('web/knowledge/events.html.twig', [
            'controller_name' => 'KnowledgeController',
            'eventsCalendar' => json_encode($eventsCalendar),
            'events' => $events,
            'month' => $month ? $month : "",
            'year' => $year ? $year : "",
            'relatedEvents' =>  $relatedEvents,
            'relatedPublications' => $relatedPublications,
            'activities'=> $activities,
            'offices'=> $offices,

        ]);
    }

    public function detail(Request $request, EventRepository $EventRepository,NavigationService $navigation, PublicationRepository $publicationRepository)
    {
        // $paises = $this->soap->getPaises('es')->getContent();
        $query = "Select * From GC_paises order by nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $paises = $stmt->fetchAll();


        $event = $EventRepository->getInstanceByRequest($request);
        $relatedEvents = $EventRepository->findByActivities($event->getActivities());
        $relatedPublications = $publicationRepository->findByActivities($event->getActivities());

        foreach ($event->getPrograms() as $key => $value) {
            $value->timeStart = $value->getDateTime()->format('H:i');
            if(isset($event->getPrograms()[$key+1])){
                $value->timeEnd = $event->getPrograms()[$key+1]->getDateTime()->format('H:i');
            }
            // dd($value->getPeople());
        }
        // dd($event);
       
        $attachmentPublished = [];
        foreach($event->getAttachments() as $attachment)
        {
            if ($attachment->isPublished($navigation->getLanguage(),$navigation->getRegion()))
                array_push($attachmentPublished,$attachment);
        }

        return $this->render('web/knowledge/eventDetail.html.twig', [
            'event' => $event,
            'attachmentPublished' => $attachmentPublished,
            'paises' => $paises,
            'relatedEvents' =>$relatedEvents,
            'relatedPublications' => $relatedPublications
        ]);
    }

    public function detail2(Request $request, EventRepository $EventRepository)
    {
        // setlocale(LC_ALL,"es_ES");
        // $EventTranslation = $EventTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        // $event = $EventRepository->findOneBy(['id' => $EventTranslation->getTranslatable()->getId()]);
        //$event = $EventRepository->getInstanceByRequest($request);
        // $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/knowledge/eventDetail2.html.twig', [

        ]);
    }

    public function ajaxActionEvent(Request $request, EventRepository $EventRepository,NavigationService $navigation)    
    {
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $title = $request->query->get('title');
        $activity = $request->query->get('activity');
        $office = $request->query->get('office');

        if( !$month ||  !$year){
            $fechaHoy = new \DateTime();
            if(!$month){
                $month = $fechaHoy->format('m');
            }
            if(!$year){
                $year = $fechaHoy->format('Y');
            }
        }

        $fecha = new \DateTime($year.'-'.$month.'-01');
        $lastday = date('t',strtotime($fecha->format('Y-m-d H:i:s')));
        $fechaFin = new \DateTime($year.'-'.$month.'-'.$lastday);
        $url= "";
        $query = $EventRepository->createPublishedQueryBuilder('e');
        if ($activity) {
            $query = $query->innerJoin('e.activities', 'a')
                ->andWhere('a.id = :activity')
                ->setParameter('activity', $activity);
            if ($url == "") {
                $url= "?activity=".$activity;
            } else {
                $url= $url . "&activity=".$activity;
            }
        }
        if ($office) {
            $query = $query->innerJoin('e.office', 'o')
                ->andWhere('e.office = :city')
                ->setParameter('city', $office);
            if ($url == "") {
                $url= "?office=".$office;
            } else {
                $url= $url . "&office=".$office;
            }
        }
        $query = $query->andWhere('e.startDate BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', $fecha->format('Y-m-d H:i:s') )
        ->setParameter('endDate', $fechaFin->format('Y-m-d H:i:s') )
        ->orderBy('e.startDate','ASC') ;

        $queryPrior = clone $query;

        $eventsAll = $query->getQuery()->getResult();

        // ----------------
        $place = $navigation->getParams()->get('app.office_place')[$navigation->getRegion()];        
        $queryPrior->join('e.office', 'o')->andWhere('o.place = :place')->setParameter('place',  $place);
        // -------------------
        $eventsPrior = $queryPrior->getQuery()->getResult();


       // dd($eventsPrior);

        $events = [];
        foreach ($eventsPrior as $key => $item) {
            $item->setCapacity(1);
            $events[$item->getId()] = $item;
        }
  
        foreach ($eventsAll as $key => $item) {
            if (!isset($events[$item->getId()])){
                $item->setCapacity(0);
                array_push($events, $item);
            }
        }  


        // if($activity){
        //     $sql = "SELECT e FROM App:Event e  WHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."' and e.activities=".$activity;
        // }else{
        //     $sql = "SELECT e FROM App:Event e WHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."'";
        // }
        // if($office){
        //     $sql = $sql . "AND e.office=".$office;
        // }
        // $events = $this->getDoctrine()
        //                 ->getManager()
        //                 ->createQuery($sql)
        //                 ->getResult();
        
        $eventsCalendar = array();
        foreach ($events as $key => $event) {
            if($event->translate('es')->getSlug()){
                $activities = "";
                foreach ($event->getActivities() as $keyActivity => $activity) {
                    $activities = $activities . $activity->translate('es')->getTitle();
                }

                $array = array(
                    "priorizado" => $event->getCapacity(),
                    "title" => $event->translate('es')->getTitle(),
                    "titleURL" => $this->container->get('router')->generate('events_detail', array('slug' => $event->translate('es')->getSlug())),
                    "start" => $event->getStartDate()->format('Y-m-d\TH:i:s.uP'),
                    "end" => $event->getEndDate()->format('Y-m-d\TH:i:s.uP'),
                    "sector" => $activities,
                    "place" => $event->translate('es')->getCustomAddress(),
                    "placeLink" => "",
                    "fullDate" => $event->getStartDate()->format('j-F'),
                    "fullTime" => $event->getStartDate()->format('H:i') ."-".$event->getEndDate()->format('H:i'),
                    "button" => "Inscribirme",
                    "speakersTitle" => "Ponentes",
                    "speakers" =>  array( )
                );
                foreach ($event->getPeople() as $keySpeaker => $speaker) {
                    if($speaker->getLawyer()){
                        $speakerName = $speaker->getLawyer()->getName() .' '. $speaker->getLawyer()->getSurname();
                        $speakerURL = 'abogados/'.$speaker->getLawyer()->getSlug();
                    }else{
                        $speakerName =$speaker->getName() .' '. $speaker->getSurname();
                        $speakerURL='';
                    }
                    $speaker = array(
                        "speaker_name" => $speakerName,
                        "speaker_url" => $speakerURL,
                    );
                    array_push($array['speakers'],$speaker);
                }
                if($title && $title!=""){
                    if(strpos($event->translate('es')->getTitle(), $title) !== false){
                        array_push($eventsCalendar,$array);
                    }
                }else{
                    array_push($eventsCalendar,$array);
                }
                
            }   
        }
        if ($eventsCalendar) {         
            return new JsonResponse($eventsCalendar);
        }

        return new JsonResponse($eventsCalendar['Results']=false);
    } 
    public function ajaxActionContact(Request $request)    
    {
        $contacto = $request->query->get('contacto');
        $contactoA = json_decode($contacto, true);
        $contactoA['CreatedShortName']='';
        $contactoA['GDPR']=1;
        $contactoA['Guid']='';
        $contactoA['IdAccount']='';
        $contactoA['IdContacto']='';
        $contactoA['IdIdioma']='';
        $contactoA['IdOrigenContacto']="Evento";
        $contactoReturn = $this->soap->createContactoForGestionEventos(array('contactoGestionEventosCreateParamDto'=>($contactoA)));
        return new JsonResponse($contactoReturn);
    }
    
    public function ajaxActionRegions(Request $request)
    {
        $idCountry = $request->query->get('idCountry');
        $query = "Select * From GC_provincias where IdPais='".$idCountry."' order by nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $regions = $stmt->fetchAll();
        //$regions = $this->soap->getProvincias('es',$idCountry)->getContent();
        return new JsonResponse($regions);

    }
}

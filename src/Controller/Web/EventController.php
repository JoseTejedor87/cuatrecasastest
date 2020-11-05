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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;


class EventController extends WebController
{
    private $soap;
    private $em;
    private $conn;
    protected $imagineCacheManager;

    public function __construct(ContainerInterface $container, CacheManager $imagineCacheManager)
    {
        $this->soap  = new SOAPContactsClientController;
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->conn = $this->em->getConnection();
        $this->imagineCacheManager = $imagineCacheManager;
    }
    
    protected function getPhotoPathByFilter($publication, $filter,$navigation)
    {
        if ($photos = $publication->getAttachments()) {
            foreach ($photos as $key => $photo) {
                if ($photo->isPublished($navigation->getLanguage(),$navigation->getRegion())){
                    if ($photo->getType() == "publication_main_photo" ) {
                        $photo = $this->imagineCacheManager->getBrowserPath(
                            '/resources/' . $photo->getFileName(),
                            $filter
                        );
                        return $photo;
                    }
                }
            }
        }
    }

    
    public function index(Request $request, EventRepository $EventRepository, NavigationService $navigation,PublicationRepository $publicationRepository, OfficeRepository $OfficeRepository,  ActivityRepository $ActivityRepository)
    {
  
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $activity = $request->query->get('activity');
        $office_id = $request->query->get('office');
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
        if ($office_id) {
            $query = $query->innerJoin('e.office', 'o_tbl')
                ->andWhere('o_tbl.id = :office_id')
                ->setParameter('office_id', $office_id);
            if ($url == "") {
                $url= "?office=".$office_id;
            } else {
                $url= $url . "&office=".$office_id;
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
        return $this->render('web/events/index.html.twig', [
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
        $headerImage = '';
        foreach($event->getAttachments() as $attachment)
        {
            if ($attachment->isPublished($navigation->getLanguage(),$navigation->getRegion())){       
                array_push($attachmentPublished,$attachment);
            }
            
        }
        $headerImage = $this->getPhotoPathByFilter($event, 'full_header',$navigation);
        




        return $this->render('web/events/detail.html.twig', [
            'event' => $event,
            'attachmentPublished' => $attachmentPublished,
            'paises' => $paises,
            'relatedEvents' =>$relatedEvents,
            'relatedPublications' => $relatedPublications,
            'headerImage' => $headerImage
        ]);
    }

    public function ajaxActionEvent(Request $request, EventRepository $EventRepository,NavigationService $navigation)    
    {
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $title = $request->query->get('title');
        $activity = $request->query->get('activity');
        $office_id = $request->query->get('office');

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

        if ($title) {
            $query = $query->innerJoin('e.translations', 't')
                            ->andWhere('t.title LIKE :title')
                            ->setParameter('title', '%'.$title.'%');   
            if ($url == "") {
                $url= "?title=".$title;
            } else {
                $url= $url . "&title=".$title;
            }
        }
        
        if ($office_id) {
            $query = $query->innerJoin('e.office', 'o_tbl')
                ->andWhere('o_tbl.id = :office_id')
                ->setParameter('office_id', $office_id);
            if ($url == "") {
                $url= "?office=".$office_id;
            } else {
                $url= $url . "&office=".$office_id;
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
        $enventIdGC = $request->query->get('enventIdGC');
        $enventId = $request->query->get('enventId');
        $contactoA = json_decode($contacto, true);
        $contactoA['CreatedShortName']='';
        $contactoA['GDPR']=1;
        $contactoA['Guid']='';
        $contactoA['IdAccount']='';
        $contactoA['IdContacto']='';
        $contactoA['IdIdioma']='';
        $contactoA['IdOrigenContacto']="Evento";
        $contactoReturn = $this->soap->createContactoForGestionEventos(array('contactoGestionEventosCreateParamDto'=>($contactoA)));
        $contactoReturnA = json_decode($contactoReturn->getContent());
        $contactoEventoA['Guid']=$contactoReturnA->Guid;
        $contactoEventoA['IdEvento']=$enventIdGC;
        $contactoEventoA['IdEventoWeb']=$enventId;
        $contactoReturnEvent = $this->soap->createEventoAsistenteForGestionEventos(array('eventoAsistenteGestionEventosCreatePatamDto'=>($contactoEventoA)));
        return new JsonResponse($contactoReturnEvent);
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

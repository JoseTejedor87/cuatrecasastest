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

    public function index(Request $request, EventRepository $EventRepository, PublicationRepository $publicationRepository)
    {
  
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $relatedEvents = $EventRepository->findByActivities('');
        $relatedPublications = $publicationRepository->findByActivities('');
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
        $events = $this->getDoctrine()
                        ->getManager()
                        ->createQuery("SELECT e FROM App:Event e WHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."'")
                        ->getResult();

        $eventsCalendar = array();
        foreach ($events as $key => $event) {
            if($event->translate('es')->getSlug()){
                $activities = "";
                foreach ($event->getActivities() as $keyActivity => $activity) {
                    $activities = $activities . $activity->translate('es')->getTitle();
                }

                $array = array(
                    "title" => $event->translate('es')->getTitle(),
                    "titleURL" => $event->translate('es')->getSlug(),
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
            'relatedPublications' => $relatedPublications
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

    public function ajaxActionEvent(Request $request)    
    {
        $month = $request->query->get('month');
        $year = $request->query->get('year');
        $title = $request->query->get('title');

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
        $events = $this->getDoctrine()
                        ->getManager()
                        ->createQuery("SELECT e FROM App:Event e WHERE e.startDate BETWEEN '".$fecha->format('Y-m-d H:i:s')."' AND  '".$fechaFin->format('Y-m-d H:i:s')."'")
                        ->getResult();
        
        $eventsCalendar = array();
        foreach ($events as $key => $event) {
            if($event->translate('es')->getSlug()){
                $activities = "";
                foreach ($event->getActivities() as $keyActivity => $activity) {
                    $activities = $activities . $activity->translate('es')->getTitle();
                }

                $array = array(
                    "title" => $event->translate('es')->getTitle(),
                    "titleURL" => $event->translate('es')->getSlug(),
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

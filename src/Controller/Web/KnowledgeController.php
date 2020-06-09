<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;


use App\Controller\Web\WebController;



class KnowledgeController extends WebController
{

    public function insights()
    {
        return $this->render('web/knowledge/insights.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    public function featured()
    {
        return $this->render('web/knowledge/featured.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    public function articleDetail()
    {
        return $this->render('web/knowledge/articleDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    public function productDetail()
    {
        return $this->render('web/knowledge/productDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    // TEMPORAL >>> BORRAR
    public function filter()
    {
        return $this->render('web/knowledge/filter.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }

    public function events(Request $request, EventRepository $eventRepository)
    {
        $month = $request->query->get('month');
        $year = $request->query->get('year');

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
        ]);
    }

    public function eventDetail(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository)
    {
        setlocale(LC_ALL,"es_ES");
        $EventTranslation = $EventTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $event = $EventRepository->findOneBy(['id' => $EventTranslation->getTranslatable()->getId()]);
        // $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/knowledge/eventDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
            'event' => $event,
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
                    }else{
                        $speakerName =$speaker->getName() .' '. $speaker->getSurname();
                    }
                    $speaker = array(
                        "speaker_name" => $speakerName,
                        "speaker_url" => "",
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

        return new Response('This is not ajax!', 400);
    } 
}

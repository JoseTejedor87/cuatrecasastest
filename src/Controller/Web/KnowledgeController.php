<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;

use App\Controller\Web\WebController;


/**
* @Route("/{idioma}/knowledge", name="knowledge")
*/
class KnowledgeController extends WebController
{
    /**
     * @Route("/insights", name="insights")
     */
    public function insights()
    {
        return $this->render('web/knowledge/insights.html.twig', [
            'controller_name' => 'KnowledgeController',
        ]);
    }


    /**
     * @Route("/events", name="events", methods={"GET"})
     */
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
        //dd(json_encode($eventsCalendar));
        return $this->render('web/knowledge/events.html.twig', [
            'controller_name' => 'KnowledgeController',
            'eventsCalendar' => json_encode($eventsCalendar),
            'events' => $events

        ]);
    }

    /**
     * @Route("/eventDetail/{slug}", name="eventDetail")
     */
    public function eventDetail(Request $request, EventTranslationRepository $EventTranslationRepository, EventRepository $EventRepository)
    {
        $EventTranslation = $EventTranslationRepository->findOneBy(['slug' => $request->attributes->get('slug')]);
        $event = $EventRepository->findOneBy(['id' => $EventTranslation->getTranslatable()->getId()]);
        $this->isThisLocale($request, $request->attributes->get('idioma'));
        return $this->render('web/knowledge/eventDetail.html.twig', [
            'controller_name' => 'KnowledgeController',
            'event' => $event,
        ]);
    }
}

<?php

namespace App\Controller\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Web\WebController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\LawyerRepository;

class ajaxGetLawersController extends WebController
{
    /**                                                                                   
     * @Route("/ajax", name="recherche_ajax")
     */
    public function ajaxAction(Request $request, LawyerRepository $lawyerRepository,NavigationService $navigation)  
    {
        // aa;
        $initial = $request->get('initial');
        $page = $request->get('page');
        if(!isset($page))
            $page = 1;  
            $limit = 18;
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
                if($lawyers){
                    $countLawyers = count($query->getResult());
                    $pagesTotal = $countLawyers/$limit;
                    if(is_float($pagesTotal)){
                        $pagesTotal = intval($pagesTotal + 1);
                    }
                }
                
                //dd($lawyers);
            }
            if($lawyers){
                $lawyerA = array ();
                foreach ($lawyers as $key => $lawyer) {
                    $lawyerA[$key] = array( 'FullName' => $lawyer->getName(). ' ' .  $lawyer->getSurname(), 'LawyerType' => $lawyer->getLawyerType(), 'Slug' => $lawyer->getSlug());
                        $activities = "";
                        foreach ($lawyer->getActivities() as $activity) {
                            $activities = $activities. ' ' . $activity->translate('es')->getTitle();
                        }
                        $lawyerA[$key]['activities'] = $activities;
                        $lawyerA[$key]['office'] = $lawyer->getOffice()->translate($navigation->getLanguage())->getCity();
                    
                }
            }
        if ($request->isXMLHttpRequest()) {         
            return new JsonResponse(array('lawyers' => $lawyerA,'countLawyers' => $countLawyers,'pagesTotal' => $pagesTotal ,'page' => $page,'initial' => $initial));
        }

        return new Response('This is not ajax!', 400);
    }  

    public function ajaxActionEvent(Request $request, LawyerRepository $lawyerRepository)    
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
                    "placeLink" => "test place",
                    "fullDate" => "test full date",
                    "fullTime" => "test time",
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
        if ($request->isXMLHttpRequest()) {         
            return new JsonResponse($eventsCalendar);
        }

        return new Response('This is not ajax!', 400);
    }  
}

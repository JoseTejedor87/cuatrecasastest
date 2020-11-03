<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\EventCategoryType;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Event;
use App\Entity\Question;
use App\Entity\Person;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Controller\CMS\CMSController;

class EventController extends CMSController
{
    // IMPORTANTE CAMPOS OBLIGATORIOS PASADOS POR CLIENTE
    // Campos obligatorios: solo deben ser obligatorios los siguientes campos: "Fecha Inicio", "Fecha Final", "Hora Inicio", "Hora Final" y "Título del evento"  , id_evento_web, idTIpoWeb, tiponombre, urlics(Hay que implementarlo), urlweb
    // Null: aforo, areas, ciudad, contacto, id estado web si publicado o no publicado, id_oficina, oficina nombre, optional adress, Ponentes externos, ponentes internos, preguntas eventos, responsables marketing, secretarias , socios , urlimagenemail
    //URLWEB: Solo slug, tiene q estar la url completa
    
    private $url;
    public function __construct()
    {
        $this->url = 'http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl';

    }
    public function index(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filter = $this->filter($request);

        if ( $filter['fields'] != ''){
            $result = $eventRepository->findFilteredBy($filter['fields']);
        }else{
            $result = $eventRepository->findBy(array(), array('startDate' => 'DESC'));
        }
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/event/index.html.twig', [
            'pagination' => $pagination,
            'formForFilterView' => $filter['form']->createView(),
        ]);
    }

    private function filter(Request $request){
        $formForFilter = $this->createFormBuilder(array(),[ 'translation_domain' => 'admin'])
            ->setMethod('GET')
            ->add('title', TextType::class, ['required' => false, 'label' => false ])
            ->add('eventType', EventCategoryType::class, ['required' => false,'label'=> false ])
            ->add('inicioDesde', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('inicioHasta', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('finDesde', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('finHasta', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('languages', LanguageType::class, ['label'=>false])
            ->add('regions', RegionType::class, ['label'=>false])
            ->add('send', SubmitType::class,['label'=> 'Filtrar' ])
            ->getForm();
    
        $formForFilter->handleRequest($request);
        $filterFields = '';

        if ($formForFilter->isSubmitted() && $formForFilter->isValid()) {
            $filterFields = $formForFilter->getData();
        }

        return array('form' => $formForFilter, 'fields' => $filterFields);
    }

    public function new(Request $request): Response
    {
      
        $event = new Event();
        $entityManager = $this->getDoctrine()->getManager();
        $eventRepository = $entityManager->getRepository(Event::class);
        $eventlast = $eventRepository->findBy(array(),array('oldId'=>'DESC'),1,0);
        $OldId = $eventlast[0]->getOldId();
        $form = $this->createForm(EventFormType::class, $event,[
            'entityManager' => $entityManager
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Crear Evento WS
            $responsablesmarketing = $this->getResponsablesmarketingSW($form,$entityManager);
            $secretarias = $this->getSecretariasSW($form,$entityManager);
            $sociosresponsables = $this->getSociosresponsablesSW($form,$entityManager);
            $eventoWS = $this->eventoSW($form,$responsablesmarketing,$secretarias, $sociosresponsables,1,$OldId+1);
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateEventoForGestionEventos($eventoWS);
            $data = $res->CreateEventoForGestionEventosResult;
            $dataEventoWS = ((array)$data);
            if($dataEventoWS['Result'] == true){
            $event->setIdGestorEventos($dataEventoWS['Data']->Id);
            $event->setOldId($dataEventoWS['Data']->IdEventoWeb);
            foreach ($responsablesmarketing as $key => $value) {
                $personRepository = $entityManager->getRepository(Person::class);
                $person = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                if(!$person){
                    $person = new Person();
                    $person->setName($value['Nombre']);
                    $person->setSurname($value['Apellidos']);
                    $person->setInicial($value['Iniciales']);
                    $person->setType('marketing');
                }
                $event->addPerson($person);
            }
            foreach ($secretarias as $key => $value) {
                $personRepository = $entityManager->getRepository(Person::class);
                $person = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                if(!$person){
                    $person = new Person();
                    $person->setName($value['Nombre']);
                    $person->setSurname($value['Apellidos']);
                    $person->setInicial($value['Iniciales']);
                    $person->setType('secretaria');

                }
                $event->addPerson($person);
            }
            foreach ($sociosresponsables as $key => $value) {
                $personRepository = $entityManager->getRepository(Person::class);
                $person = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                if(!$person){
                    $person = new Person();
                    $person->setName($value['Nombre']);
                    $person->setSurname($value['Apellidos']);
                    $person->setInicial($value['Iniciales']);
                    $person->setType('socio');
                }
                $event->addPerson($person);
            }
                $entityManager->persist($event);
                $event->mergeNewTranslations();
                $entityManager->flush();
                return $this->redirectToRoute('cms_events_index');
            }

        }

        return $this->render('cms/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function show(Event $event): Response
    {
        return $this->render('cms/event/show.html.twig', [
            'event' => $event
        ]);
    }

    public function edit(Request $request, Event $event): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(EventFormType::class, $event,[
            'entityManager' => $entityManager
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Crear Evento WS
            $responsablesmarketing = $this->getResponsablesmarketingSW($form,$entityManager);
            $secretarias = $this->getSecretariasSW($form,$entityManager);
            $sociosresponsables = $this->getSociosresponsablesSW($form,$entityManager);
            $eventoWS = $this->eventoSW($form,$responsablesmarketing,$secretarias, $sociosresponsables,0,$event->getOldId());
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');

            $res  = $client->UpdateEventoForGestionEventos($eventoWS);
            $data = $res->UpdateEventoForGestionEventosResult;
            $dataEventoWS = ((array)$data);
            if($dataEventoWS['Result'] == true){
                if($responsablesmarketing)
                foreach ($responsablesmarketing as $key => $value) {
                    $personRepository = $entityManager->getRepository(Person::class);
                    $personA = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                    if(isset($personA[0]))
                    $person = $personA[0];
                    if(!$person){
                        $person = new Person();
                        $person->setName($value['Nombre']);
                        $person->setSurname($value['Apellidos']);
                        $person->setInicial($value['Iniciales']);
                        $person->setType('marketing');
                    }
                    $event->addPerson($person);
                }
                if($secretarias)
                foreach ($secretarias as $key => $value) {
                    $personRepository = $entityManager->getRepository(Person::class);
                    $personA = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                    if(isset($personA[0]))
                    $person = $personA[0];
                    if(!$person){
                        $person = new Person();
                        $person->setName($value['Nombre']);
                        $person->setSurname($value['Apellidos']);
                        $person->setInicial($value['Iniciales']);
                        $person->setType('secretaria');
        
                    }
                    $event->addPerson($person);
                }
                if($sociosresponsables)
                foreach ($sociosresponsables as $key => $value) {
                    $personRepository = $entityManager->getRepository(Person::class);
                    $personA = $personRepository->findBy(['inicial' => $value['Iniciales']]);
                    if(isset($personA[0]))
                    $person = $personA[0];
                    if(!$person){
                        $person = new Person();
                        $person->setName($value['Nombre']);
                        $person->setSurname($value['Apellidos']);
                        $person->setInicial($value['Iniciales']);
                        $person->setType('socio');
                    }
                    $event->addPerson($person);
                }
    
                if (isset($request->request->get('event_form')['attachments'])) {
                    $attachments = $request->request->get('event_form')['attachments'];
                    foreach ($attachments as $key => $attachment) {
                        if (isset($attachment['file']['delete']) && $attachment['file']['delete'] == "1") {
                            $event->removeAttachment(
                                $event->getAttachments()[$key]
                            );
                        }
                    }
                }
    
                $this->getDoctrine()->getManager()->flush();
    
                return $this->redirectToRoute('cms_events_edit', ['id'=>$event->getId()]);
            }
            
        }

        $a = $form->createView();

        return $this->render('cms/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_events_index');
    }
    public function getResponsablesmarketingSW($form, $entityManager){
        $responsablesmarketing = array();
            foreach ($form->get('responsablesmarketing')->getData() as $key => $value) {
                array_push($responsablesmarketing, "'".$value."'");
            }
            
            if($responsablesmarketing){
                $conn = $entityManager->getConnection();
                $sql = "SELECT * FROM gc_responsablesmarketings where Iniciales in (". implode(",", $responsablesmarketing) .")";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $responsablesmarketingA =$stmt->fetchAll();
                return  $responsablesmarketingA;
            }
            return null;
    }
    public function getSecretariasSW($form, $entityManager){
        $secretarias = array();
            foreach ($form->get('secretarias')->getData() as $key => $value) {
                array_push($secretarias,"'".$value."'");
            }
            if($secretarias){
                $conn = $entityManager->getConnection();
                $sql = "SELECT * FROM gc_secretarias where Iniciales in (". implode(",", $secretarias) .")";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $secretariasA =$stmt->fetchAll();
                return  $secretariasA;
            }
            return null;
    }
    public function getSociosresponsablesSW($form, $entityManager){
        $sociosresponsables = array();
            foreach ($form->get('sociosresponsables')->getData() as $key => $value) {
                array_push($sociosresponsables,"'".$value."'");
            }
            if($sociosresponsables){
                $conn = $entityManager->getConnection();
                $sql = "SELECT * FROM gc_sociosresponsables where Iniciales in (". implode(",", $sociosresponsables) .")";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $sociosresponsablesA =$stmt->fetchAll();
                return  $sociosresponsablesA;
            }
            return null;
    }
    public function eventoSW($form,$responsablesmarketing,$secretarias,$sociosresponsables,$new,$oldId){
        if($new){
            $type= 'eventoGestionEventosCreateParamDto';
        }else{
            $type= 'eventoGestionEventosUpdateParamDto';
        }
        $parametrosEvento = array( $type=>( 
            array ( 
                'Aforo' => 6 ,
                'Areas' =>  array (),
                'Ciudad' => "" ,
                'Contacto' =>  array (),
                'CreatedShortName' => "" ,
                'EstadoNombre' => "" ,
                'FechaFin' => "" ,
                'FechaInicio' => "" ,
                'IdEstadoWeb' => "" ,
                'IdEventoWeb' => "" ,
                'IdOficina' => "" ,
                'IdTipoWeb' => "" ,
                'OficinaNombre' => "" ,
                'OptionalAddress' =>  array (),
                'PonentesExternos' =>  array (),
                'PonentesInternos' =>  array (),
                'PreguntasEvento' =>  array (),
                'ResponsablesMarketing' =>  array (),
                'Secretarias' =>  array (),
                'SociosResponsables' =>  array (),
                'TipoNombre' => "" ,
                'Titulo' => "" ,
                'UrlIcs' => "" ,
                'UrlImagenEmail' => "" ,
                'UrlWeb' => "" ,
                    
            )
        ));
        $parametrosEvento[$type]['Aforo'] = $form->get('capacity')->getData();
        $parametrosEvento[$type]['Areas'] = array();
        $parametrosEvento[$type]['Ciudad'] = $form->get('office')->getData()->translate('es')->getCity();
        $parametrosEvento[$type]['Contacto']['Email'] = $form->get('email')->getData();
        $parametrosEvento[$type]['Contacto']['Name'] = $form->get('contact')->getData();
        $parametrosEvento[$type]['Contacto']['Phone'] = $form->get('phone')->getData();
        $parametrosEvento[$type]['CreatedShortName'] ='EXT4';
        $parametrosEvento[$type]['EstadoNombre'] = $form->get('published')->getData() ? 'activo' : 'inactivo' ;
        $parametrosEvento[$type]['FechaFin'] = $form->get('endDate')->getData()->format('Y-m-d\TH:i:s');;
        $parametrosEvento[$type]['FechaInicio'] = $form->get('startDate')->getData()->format('Y-m-d\TH:i:s');;
        $parametrosEvento[$type]['IdEstadoWeb'] = $form->get('published')->getData() ? '2' : '1' ;
        // REVISAR
        $parametrosEvento[$type]['IdEventoWeb'] = $new ? $oldId : $oldId;
        $parametrosEvento[$type]['IdOficina'] = $form->get('office')->getData()->getSap();
        $parametrosEvento[$type]['IdTipoWeb'] = $form->get('eventType')->getData()=='standard' ? '1' : $form->get('eventType')->getData()=='webinar' ? '2' : $form->get('eventType')->getData()=='breakfast' ? '3' : $form->get('eventType')->getData()=='institutional' ? '4' :'' ;
        $parametrosEvento[$type]['OficinaNombre'] = $form->get('office')->getData()->translate('es')->getCity();
        
        if($form->get('translations')->getData()['es']->getCustomAddress()){
            $parametrosEvento[$type]['OptionalAddress']['Address'] = $form->get('translations')->getData()['es']->getCustomAddress();
            $parametrosEvento[$type]['OptionalAddress']['City'] = $form->get('translations')->getData()['es']->getCustomCity();
            $parametrosEvento[$type]['OptionalAddress']['Country'] = $form->get('translations')->getData()['es']->getCustomCountry();
            $parametrosEvento[$type]['OptionalAddress']['PostalCode'] = $form->get('translations')->getData()['es']->getCustomPostalcode();
            $parametrosEvento[$type]['OptionalAddress']['Province'] = $form->get('translations')->getData()['es']->getCustomProvince();
        }
        foreach ($form->get('people')->getData() as $key => $value) {
            if($value->getLawyer()){
                $parametrosEvento[$type]['PonentesExternos']['EventoGestionEventosPonenteInternoCreateParamDto'] = array( 'Apellidos' => $value->getSurname(), 'Iniciales' => $value->getInicial() ,'Nombre' => $value->getName());
            }else{
                $parametrosEvento[$type]['PonentesExternos']['EventoGestionEventosPonenteInternoCreateParamDto'] = array('Apellidos' => $value->getSurname(),'Nombre' => $value->getName());
            }
        }
        foreach ($form->get('questions')->getData() as $key => $value) {
                $parametrosEvento[$type]['PreguntasEvento']['EventoPreguntaCreateDto'] = array( 'Action' => $new ? "INSERT" : "EDIT", 'IdEventQuestionWeb' => $value->translate('es')->getHash() ?  $value->translate('es')->getHash() : md5($value->translate('es')->getQuestion()) ,'Question' => $value->translate('es')->getQuestion());
        }
        if($responsablesmarketing)
        foreach ($responsablesmarketing as $key => $value) {
                $parametrosEvento[$type]['ResponsablesMarketing']['EventoGestionEventosResponsableMarketingCreateParamDto'] = array( 'Apellidos' => $value['Apellidos'], 'Iniciales' => $value['Iniciales'] ,'Nombre' => $value['Nombre']);
        }
        if($secretarias)
        foreach ($secretarias as $key => $value) {
                $parametrosEvento[$type]['Secretarias']['EventoGestionEventosSecretariaCreateParamDto'] = array( 'Apellidos' => $value['Apellidos'], 'Iniciales' => $value['Iniciales'] ,'Nombre' => $value['Nombre']);      
        }
        if($sociosresponsables)
        foreach ($sociosresponsables as $key => $value) {
                $parametrosEvento[$type]['SociosResponsables']['EventoGestionEventosSocioResponsableCreateParamDto'] = array( 'Apellidos' => $value['Apellidos'], 'Iniciales' => $value['Iniciales'] ,'Nombre' => $value['Nombre']);
        }
        $parametrosEvento[$type]['IdTipoWeb'] = $form->get('eventType')->getData()=='standard' ? 'Estándar' : $form->get('eventType')->getData()=='webinar' ? 'Webinar' : $form->get('eventType')->getData()=='breakfast' ? 'Desayuno' : $form->get('eventType')->getData()=='institutional' ? 'Institucional' :'' ;
        $parametrosEvento[$type]['Titulo'] = $form->get('translations')->getData()['es']->getTitle();
        $parametrosEvento[$type]['UrlIcs'] = "";
        $parametrosEvento[$type]['UrlImagenEmail'] = "";
        $parametrosEvento[$type]['UrlWeb'] = $form->get('translations')->getData()['es']->getSlug() ? $form->get('translations')->getData()['es']->getSlug() : ' ';
        
       
        return $parametrosEvento;

    }
}

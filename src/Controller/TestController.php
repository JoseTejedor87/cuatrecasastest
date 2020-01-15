<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("gestorcontactos")
 */
class TestController extends AbstractController
{

    // GetContactoWebtFormExtranet(NO),  
// ,  EditEventoAsistenteRespuestasForGestionEventos
//  

// GetIdiomas, GetAreasInteres, GetOficinas, GetPaises, GetProvincias,AddContactoWebForm , CreateEventoAsistenteForGestionEventos,CancelAttendanceEventoAsistenteForGestionEventos,
// GetSecretarias, GetResponsablesMarketing, GetSociosResponsables
//GetContactoWebtForm ,CreateEventoForGestionEventos, UpdateEventoForGestionEventos, GetEventosForGestionEventos,CreateContactoForGestionEventos, ConfirmAttendanceEventoAsistenteForGestionEventos

    /**
     * @Route("/getPaises", name="getPaises", methods={"GET"})
     */
    public function getPaises(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getPaises?LanguageId=es
        if(is_array($request->query->all())){
            if($request->query->get('LanguageId')){
                $LanguageId = $request->query->get('LanguageId');
            }else{
                $LanguageId = "";
            }

            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetPaises( array ('filter' =>( array ( 'LanguageId' => $LanguageId, 'PaisId'  =>  ''))));
            $data = $res->GetPaisesResult->PaisWebDto;
            dd($data);
            return $data;

        }
        
    }
    /**
     * @Route("/getProvincias", name="getProvincias", methods={"GET"})
     */
    public function getProvincias(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getProvincias?PaisId=es
        if(is_array($request->query->all())){
            if($request->query->get('LanguageId')){
                $LanguageId = $request->query->get('LanguageId');
            }else{
                $LanguageId = "";
            }
            if($request->query->get('PaisId')){
                $PaisId = $request->query->get('PaisId');
            }else{
                $PaisId = "";
            }
            if($request->query->get('ProvinciaId')){
                $ProvinciaId = $request->query->get('ProvinciaId');
            }else{
                $ProvinciaId = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetProvincias( array ('filter' =>( array ( 'LanguageId' => $LanguageId, 'PaisId'  =>  $PaisId, 'ProvinciaId'  =>  $ProvinciaId))));
            $data = $res->GetProvinciasResult->ProvinciaWebDto;
            dd($data);
            return $data;

        }
        
    }

    /**
     * @Route("/getIdiomas", name="getIdiomas", methods={"GET"})
     */
    public function getIdiomas(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getIdiomas?IdiomaId=es
        if(is_array($request->query->all())){
            if($request->query->get('IdiomaId')){
                $IdiomaId = $request->query->get('IdiomaId');
            }else{
                $IdiomaId = "";
            }
            if($request->query->get('LanguageId')){
                $LanguageId = $request->query->get('LanguageId');
            }else{
                $LanguageId = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetIdiomas( array ('filter' =>( array ( 'IdiomaId' => $IdiomaId, 'LanguageId'  =>  $LanguageId))));
            $data = $res->GetIdiomasResult->IdiomaDto;
            return $data;

        }
        
        
        
    }
    /**
     * @Route("/getAreasInteres", name="getAreasInteres", methods={"GET"})
     */
    public function getAreasInteres(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getAreasInteres?IdiomaId=es

        if(is_array($request->query->all())){
            if($request->query->get('AreaInteresId')){
                $AreaInteresId = $request->query->get('AreaInteresId');
            }else{
                $AreaInteresId = "";
            }
            if($request->query->get('LanguageId')){
                $LanguageId = $request->query->get('LanguageId');
            }else{
                $LanguageId = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetAreasInteres( array ('filter' =>( array ( 'AreaInteresId' => $AreaInteresId, 'LanguageId'  => $LanguageId))));
            $data = $res->GetAreasInteresResult->AreaInteresWebDto;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getOficinas", name="getOficinas", methods={"GET"})
     */
    public function getOficinas(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getOficinas?OficinaId=WALC

        if(is_array($request->query->all())){
            if($request->query->get('OficinaId')){
                $OficinaId = $request->query->get('OficinaId');
            }else{
                $OficinaId = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetOficinas( array ('filter' =>( array ( 'OficinaId' => $OficinaId))));
            $data = $res->GetOficinasResult->OficinaWebDto;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getSecretarias", name="getSecretarias", methods={"GET"})
     */
    public function getSecretarias(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSecretarias?Iniciales=JH

        if(is_array($request->query->all())){
            if($request->query->get('Iniciales')){
                $Iniciales = $request->query->get('Iniciales');
            }else{
                $Iniciales = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetSecretarias( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetSecretariasResult->SecretariaWebDto;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getResponsablesMarketing", name="getResponsablesMarketing", methods={"GET"})
     */
    public function getResponsablesMarketing(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getResponsablesMarketing?Iniciales=LCM

        if(is_array($request->query->all())){
            if($request->query->get('Iniciales')){
                $Iniciales = $request->query->get('Iniciales');
            }else{
                $Iniciales = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetResponsablesMarketing( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetResponsablesMarketingResult->ResponsableMarketingWebDto;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getSociosResponsables", name="getSociosResponsables", methods={"GET"})
     */
    public function getSociosResponsables(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSociosResponsables?Iniciales=JCV

        if(is_array($request->query->all())){
            if($request->query->get('Iniciales')){
                $Iniciales = $request->query->get('Iniciales');
            }else{
                $Iniciales = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetSociosResponsables( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetSociosResponsablesResult->SocioResponsableWebDto;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getContactoWebtForm", name="getContactoWebtForm", methods={"GET"})
     */
    public function getContactoWebtForm(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getContactoWebtForm?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetContactoWebtForm( array ('filter' =>( array ( 'Guid' => $Guid))));
            $data = $res->GetContactoWebtFormResult;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/createEventoForGestionEventos", name="createEventoForGestionEventos", methods={"GET","POST"})
     */
    public function createEventoForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createEventoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->EventoCreate("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateEventoForGestionEventos($parametros);
            $data = $res->CreateEventoForGestionEventosResult;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/updateEventoForGestionEventos", name="updateEventoForGestionEventos", methods={"GET","POST"})
     */
    public function updateEventoForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/updateEventoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->EventoUpdate("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateEventoForGestionEventos($parametros);
            $data = $res->UpdateEventoForGestionEventos;
            dd($data);
            return $data;

        }
    }

    /**
     * @Route("/deleteEventoForGestionEventos", name="deleteEventoForGestionEventos", methods={"GET"})
     */
    public function deleteEventoForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/deleteEventoForGestionEventos?DeletedShortName=EXT4&IdEventoWeb=19169

        if(is_array($request->query->all())){
            if($request->query->get('DeletedShortName')){
                $DeletedShortName = $request->query->get('DeletedShortName');
            }else{
                $DeletedShortName = "";
            }
            if($request->query->get('IdEventoWeb')){
                $IdEventoWeb = $request->query->get('IdEventoWeb');
            }else{
                $IdEventoWeb = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->DeleteEventoForGestionEventos( array ('eventoGestionEventosDeleteParamDto' =>( array ( 'DeletedShortName' => $DeletedShortName, 'IdEventoWeb'  => $IdEventoWeb))));
            $data = $res->DeleteEventoForGestionEventosResult->Result;
            dd($data);
            return $data;

        }
    }
    /**
     * @Route("/getEventosForGestionEventos", name="getEventosForGestionEventos", methods={"GET"})
     */
    public function getEventosForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getEventosForGestionEventos?GetFullData=1&Id=20171&IdEventoWeb=19168

        if(is_array($request->query->all())){
            if($request->query->get('GetFullData')){
                $GetFullData = $request->query->get('GetFullData');
            }else{
                $GetFullData = 1;
            }
            if($request->query->get('Id')){
                $Id = $request->query->get('Id');
            }else{
                $Id = "";
            }
            if($request->query->get('IdEventoWeb')){
                $IdEventoWeb = $request->query->get('IdEventoWeb');
            }else{
                $IdEventoWeb = "";
            }
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetEventosForGestionEventos( array ('eventoGestionEventosFilterParamDto' =>( array ( 'GetFullData' => $GetFullData, 'Id' =>  array ( 'int' => $Id ), 'IdEventoWeb'  =>  array ( 'int' => $IdEventoWeb )))));
            $data = $res->GetEventosForGestionEventosResult->Data;
            dd($res);
            return $data;

        }
    }
    /**
     * @Route("/createContactoForGestionEventos", name="createContactoForGestionEventos", methods={"GET","POST"})
     */
    public function createContactoForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createContactoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->Contacto("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateContactoForGestionEventos($parametros);
            $data = $res->CreateContactoForGestionEventosResult->Data;
            dd($data);
            return $data;

        }
    }
    
    /**
     * @Route("/createEventoAsistenteForGestionEventos", name="createEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function createEventoAsistenteForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->eventoAsistente("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateEventoAsistenteForGestionEventos($parametros);
            $data = $res;
            dd($data);
            return $data;

        }
    }

    /**
     * @Route("/confirmAttendanceEventoAsistenteForGestionEventos", name="confirmAttendanceEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function confirmAttendanceEventoAsistenteForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/confirmAttendanceEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->confirmCancelEventoAsistente("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->ConfirmAttendanceEventoAsistenteForGestionEventos($parametros);
            $data = $res;
            dd($data);
            return $data;

        }
    }


    /**
     * @Route("/cancelAttendanceEventoAsistenteForGestionEventos", name="cancelAttendanceEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function cancelAttendanceEventoAsistenteForGestionEventos(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/cancelAttendanceEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->confirmCancelEventoAsistente("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CancelAttendanceEventoAsistenteForGestionEventos($parametros);
            $data = $res;
            dd($data);
            return $data;

        }
    }



    

    /**
     * @Route("/addContactoWebForm", name="addContactoWebForm", methods={"GET","POST"})
     */
    public function addContactoWebForm(Request $request): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/addContactoWebForm?Guid=00505693770F1EDA8B93326D22524160

        if(is_array($request->query->all())){
            if($request->query->get('Guid')){
                $Guid = $request->query->get('Guid');
            }
            $parametros = $this->ContactoWeb("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->AddContactoWebForm($parametros);
            $data = $res;
            dd($data);
            return $data;

        }
    }
    public function EventoCreate($param)
    {
        $parametrosEvento = array('eventoGestionEventosCreateParamDto'=>( 
            array ( 
                'Aforo' => 50 ,
                'Areas' =>  array ( 
                    'EventoGestionEventosAreaCreateParamDto' => array ( 
                        'IdArea' => "WE",
                        'Nombre' => "Laboral"       
                    )
                ),
                'Ciudad' => "Barcelona" ,
                'Contacto' =>  array (
                    'Email' => "John.doe@cuatrecasas.com",
                    'Name' => "John Doe",
                    'Phone' => "+34 932 905 500"
                ),
                'CreatedShortName' => "EXT4" ,
                'EstadoNombre' => "activo" ,
                'FechaFin' => "2018-04-23T12:00:00.000" ,
                'FechaInicio' => "2018-04-23T17:00:00.000" ,
                'IdEstadoWeb' => 2 ,
                'IdEventoWeb' => 19169 ,
                'IdOficina' => "WBCN" ,
                'IdTipoWeb' => 1 ,
                'OficinaNombre' => "Barcelona" ,
                'OptionalAddress' =>  array (
                    'Address' => "Diagonal, 191",
                    'City' => "Barcelona",
                    'Country' => "España",
                    'PostalCode' => "08029" ,
                    'Province' => "Barcelona"
                ),
                'PonentesExternos' =>  array ( 
                    'EventoGestionEventosPonenteExternoCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Nombre' => "John" 
                    )      
                ),
                'PonentesInternos' =>  array ( 
                    'EventoGestionEventosPonenteInternoCreateParamDto' => array ( 
                        'Apellidos' => "Cuatrecasas",
                        'Iniciales' => "EC",
                        'Nombre' => "Emilio" 
                    )      
                ),
                'PreguntasEvento' =>  array ( 
                    'EventoPreguntaCreateDto' => array ( 
                        'Action' => 1,
                        'IdEventQuestionWeb' => "82dnfoaqp9dqn3gy49ptrbir4t7qvzf2",
                        'Question' => "Indique si tiene intolerancia a algún alimento, gracias."    
                    )   
                ),
                'ResponsablesMarketing' =>  array ( 
                    'EventoGestionEventosResponsableMarketingCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Iniciales' => "JNOE",
                        'Nombre' => "Jane"   
                    )    
                ),
                'Secretarias' =>  array ( 
                    'EventoGestionEventosSecretariaCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Iniciales' => "JDE",
                        'Nombre' => "John" 
                    )      
                ),
                'SociosResponsables' =>  array ( 
                    'EventoGestionEventosSocioResponsableCreateParamDto' => array ( 
                        'Apellidos' => "Cuatrecasas",
                        'Iniciales' => "EC",
                        'Nombre' => "Emilio"   
                    )    
                ),
                'TipoNombre' => "Estándar" ,
                'Titulo' => "Impacto en la fiscalidad de los vehículos y de los inversores" ,
                'UrlIcs' => "https://www.cuatrecasas.com/es/evento/calendario/93143.html" ,
                'UrlImagenEmail' => "https://www.cuatrecasas.com/media_repository/images/eventos/impactofscl.jpg" ,
                'UrlWeb' => "https://www.cuatrecasas.com/es/evento/92499/impacto_fiscalidad_movil.html" ,
                       
            )
        ));
        return($parametrosEvento);
        
    }
    public function EventoUpdate($param)
    {
        $parametrosEvento = array('eventoGestionEventosUpdateParamDto'=>( 
            array ( 
                'Aforo' => 50 ,
                'Areas' =>  array ( 
                    'EventoGestionEventosAreaCreateParamDto' => array ( 
                        'IdArea' => "WE",
                        'Nombre' => "Laboral"       
                    )
                ),
                'Ciudad' => "Barcelona" ,
                'Contacto' =>  array (
                    'Email' => "John.doe@cuatrecasas.com",
                    'Name' => "John Doe",
                    'Phone' => "+34 932 905 500"
                ),
                'EstadoNombre' => "activo" ,
                'FechaFin' => "2018-04-23T12:00:00.000" ,
                'FechaInicio' => "2018-04-23T17:00:00.000" ,
                'IdEstadoWeb' => 2 ,
                'IdEventoWeb' => 19169 ,
                'IdOficina' => "WBCN" ,
                'IdTipoWeb' => 1 ,
                'OficinaNombre' => "Barcelona" ,
                'OptionalAddress' =>  array (
                    'Address' => "Diagonal, 191",
                    'City' => "Barcelona",
                    'Country' => "España",
                    'PostalCode' => "08029" ,
                    'Province' => "Barcelona"
                ),
                'PonentesExternos' =>  array ( 
                    'EventoGestionEventosPonenteExternoCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Nombre' => "John" 
                    )      
                ),
                'PonentesInternos' =>  array ( 
                    'EventoGestionEventosPonenteInternoCreateParamDto' => array ( 
                        'Apellidos' => "Cuatrecasas",
                        'Iniciales' => "EC",
                        'Nombre' => "Emilio" 
                    )      
                ),
                'PreguntasEvento' =>  array ( 
                    'EventoPreguntaCreateDto' => array ( 
                        'Action' => 1,
                        'IdEventQuestionWeb' => "82dnfoaqp9dqn3gy49ptrbir4t7qvzf2",
                        'Question' => "Indique si tiene intolerancia a algún alimento, gracias."    
                    )   
                ),
                'ResponsablesMarketing' =>  array ( 
                    'EventoGestionEventosResponsableMarketingCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Iniciales' => "JNOE",
                        'Nombre' => "Jane"   
                    )    
                ),
                'Secretarias' =>  array ( 
                    'EventoGestionEventosSecretariaCreateParamDto' => array ( 
                        'Apellidos' => "Doe",
                        'Iniciales' => "JDE",
                        'Nombre' => "John" 
                    )      
                ),
                'SociosResponsables' =>  array ( 
                    'EventoGestionEventosSocioResponsableCreateParamDto' => array ( 
                        'Apellidos' => "Cuatrecasas",
                        'Iniciales' => "EC",
                        'Nombre' => "Emilio"   
                    )    
                ),
                'TipoNombre' => "Estándar" ,
                'Titulo' => "Impacto en la fiscalidad de los vehículos y de los inversores" ,
                'CreatedShortName' => "EXT4" ,
                'UrlIcs' => "https://www.cuatrecasas.com/es/evento/calendario/93143.html" ,
                'UrlImagenEmail' => "https://www.cuatrecasas.com/media_repository/images/eventos/impactofscl.jpg" ,
                'UrlWeb' => "https://www.cuatrecasas.com/es/evento/92499/impacto_fiscalidad_movil.html" ,
                       
            )
        ));
        return($parametrosEvento);
        
    }

    public function Contacto($param)
    {
        $parametrosEvento = array('contactoGestionEventosCreateParamDto'=>( 
            array ( 
                'Apellidos' => "Juanito" ,
                'Cargo' => "IT" ,
                'CodigoPostal' => "08030" ,
                'CreatedShortName' => "EXT" ,
                'Direccion' => "Calle cinca 71" ,
                'Email' => "josetejedor87@gmail.com" ,
                'Empresa' => "CC" ,
                'GDPR' => 1 ,
                'Guid' => "" ,
                'IdAccount' => "0000071480" ,
                'IdContacto' => "4000000596" ,
                'IdIdioma' => "" ,
                'IdOrigenContacto' => "Evento" ,
                'IdPais' => "" ,
                'IdProvincia' => "" ,
                'Nombre' => "Barcelona" ,
                'Poblacion' => "Barcelona" ,
                'Telefono' => "690367295" ,
                       
            )
        ));
        return($parametrosEvento);
        
    }

    public function ContactoWeb($param)
    {
        $parametrosEvento = array('contactoWebDto'=>( 
            array ( 
                'Accion' => 2 ,
                'Apellidos' => "Tejedor Bello" ,
                'Cargo' => "IT" ,
                'CodigoPostal' => "08030" ,
                'ContactoAreaInteres' => array(
                    'int'=> 12
                ),
                'Direccion' => "Calle cinca 71" ,
                'Email' => "josetejedor87@gmail.com" ,
                'Empresa' => "CC" ,
                'EstadoContacto' => 2,
                'FechaModificacion' => '2020-01-10T09:27:00',
                'GDPR' => 1 ,
                'Guid' => "" ,
                'IdAccount' => "" ,
                'IdContacto' => "" ,
                'IdContacto' => "" ,
                'IdEstadoEnvioWeb' => 0 ,
                'IdOrigenContacto' => "Newsletter_prensa" ,
                'IdiomaId' => "ES" ,
                'InteresList' => array(
                    'string'=> "ass"
                ),
                'MotivoBaja' => '',
                'Nombre' => 'Jose',
                'IdPais' => "ES" ,
                'Poblacion' => "Barcelona" ,
                'ProvinciaId' => '08' ,
                'Servicios' => array(
                    'ServicioContactoDto' => array(
                        'Alta'=> 1,
                        'IdServicio'=> 03
                    )
                ),
                'Suscripcion' => 0 ,
                'SuscripcionPrensa' => 1 ,
                'Telefono' => "690367295" ,
                       
            )
        ));
        return($parametrosEvento);
        
    }

    public function eventoAsistente($param)
    {
        $parametrosEvento = array('eventoAsistenteGestionEventosCreatePatamDto'=>( 
            array ( 
                'CreatedShortName' => "" ,
                'Guid' => "edb8bbd7-9246-4ba2-acd6-d035b62132a2" ,
                'IdAsistenteEstado' => "" ,
                'IdAsistenteOrigen' => "" ,
                'IdEvento' => 19170 ,
                'IdEventoWeb' => 92569 ,
                'IsCRMContact' => "" ,
                'Observaciones' => ""
                       
            )
        ));
        return($parametrosEvento);
        
    }
    public function confirmCancelEventoAsistente($param)
    {
        $parametrosEvento = array('eventoAsistenteGestionEventosAttendanceParamDto'=>( 
            array ( 
                'Guid' => "edb8bbd7-9246-4ba2-acd6-d035b62132a2" ,
                'IdEvento' => 20171 ,
                'IdEventoWeb' => 19168 ,
                'UpdatedShortName' => ""  
            )
        ));
        return($parametrosEvento);
        
    }
}

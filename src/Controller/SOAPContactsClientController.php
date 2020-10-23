<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class SOAPContactsClientController extends AbstractController
{
    private $url;
    public function __construct()
    {
        $this->url = 'http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl';

    }

    public function getPaises($LanguageId): Response
    {

            $client = new \SoapClient($this->url);
            $res  = $client->GetPaises( array ('filter' =>( array ( 'LanguageId' => $LanguageId, 'PaisId'  =>  ''))));
            $data = $res->GetPaisesResult->PaisWebDto;
            return new Response(json_encode($data));
        
    }


    public function getProvincias($LanguageId,$PaisId): Response
    {
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetProvincias( array ('filter' =>( array ( 'LanguageId' => $LanguageId, 'PaisId'  =>  $PaisId, 'ProvinciaId'  =>  ''))));
            $data = $res->GetProvinciasResult->ProvinciaWebDto;
            return new Response(json_encode($data));
    }


    public function getIdiomas(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getIdiomas?IdiomaId=es
        
        $IdiomaId = "";
        $LanguageId = "";
           
        $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
        $res  = $client->GetIdiomas( array ('filter' =>( array ( 'IdiomaId' => $IdiomaId, 'LanguageId'  =>  $LanguageId))));
        $data = $res->GetIdiomasResult->IdiomaDto;
        return new Response(json_encode($data));

        
        
        
        
    }

    public function getAreasInteres(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getAreasInteres?IdiomaId=es

        $AreaInteresId = "";
        $LanguageId = "";
        $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
        $res  = $client->GetAreasInteres( array ('filter' =>( array ( 'AreaInteresId' => $AreaInteresId, 'LanguageId'  => $LanguageId))));
        $data = $res->GetAreasInteresResult->AreaInteresWebDto;
        return new Response(json_encode($data));
    }

    public function getOficinas(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getOficinas?OficinaId=WALC

            $OficinaId = "";
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetOficinas( array ('filter' =>( array ( 'OficinaId' => $OficinaId))));
            $data = $res->GetOficinasResult->OficinaWebDto;
            return new Response(json_encode($data));
    }

    public function getSecretarias(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSecretarias?Iniciales=JH

            $Iniciales = "";
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetSecretarias( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetSecretariasResult->SecretariaWebDto;
            return new Response(json_encode($data));
    }

    public function getResponsablesMarketing(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getResponsablesMarketing?Iniciales=LCM

            $Iniciales = "";
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetResponsablesMarketing( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetResponsablesMarketingResult->ResponsableMarketingWebDto;
            return new Response(json_encode($data));
   }

    public function getSociosResponsables(): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSociosResponsables?Iniciales=JCV
            $Iniciales = "";
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetSociosResponsables( array ('filter' =>( array ( 'Iniciales' => $Iniciales))));
            $data = $res->GetSociosResponsablesResult->SocioResponsableWebDto;
            return new Response(json_encode($data));
    }


    public function getContactoWebtForm($parametros): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getContactoWebtForm?Guid=00505693770F1EDA8B93326D22524160
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetContactoWebtForm( array ('filter' =>( array ( 'Guid' => $parametros))));
            $data = $res->GetContactoWebtFormResult;
            return new Response(json_encode($data));
    }

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
            return new Response(json_encode($data));

        }
    }

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
            return new Response(json_encode($data));

        }
    }


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
            return new Response(json_encode($data));
        }
    }

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
            return new Response(json_encode($data));

        }
    }

    public function createContactoForGestionEventos($parametros): Response
    {
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateContactoForGestionEventos($parametros);
            $data = $res->CreateContactoForGestionEventosResult->Data;
            return new Response(json_encode($data));

    }
    

    public function createEventoAsistenteForGestionEventos($parametros): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->CreateEventoAsistenteForGestionEventos($parametros);
            $data = $res;
            return new Response(json_encode($data));
    }


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
            return new Response(json_encode($data));

        }
    }


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
            return new Response(json_encode($data));

        }
    }


    public function addContactoWebForm($parametros): Response
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/addContactoWebForm?Guid=00505693770F1EDA8B93326D22524160

   
            // $parametros = $this->ContactoWeb("test");
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->AddContactoWebForm($parametros);
            $data = $res;
            return new Response(json_encode($data));

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
                'IdAccount' => "" ,
                'IdContacto' => "" ,
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

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\SOAPContactsClientController;

/**
 * @Route("gestorcontactos")
*/
class TestController extends AbstractController
{
    private $soap;

    public function __construct()
    {
        $this->soap  = new SOAPContactsClientController;
    }
     /**
     * @Route("/getPaises", name="getPaises", methods={"GET"})
     */
    public function getPaises(Request $request)
    { 
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getPaises?LanguageId=es
        dd($this->soap->getPaises($request));
    }
    
    /**
     * @Route("/getProvincias", name="getProvincias", methods={"GET"})
     */
    public function getProvincias(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getProvincias?PaisId=es
        dd($this->soap->getProvincias($request));
    }
        /**
     * @Route("/getIdiomas", name="getIdiomas", methods={"GET"})
     */
    public function getIdiomas(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getIdiomas?IdiomaId=es
        dd($this->soap->getIdiomas($request));
    }
    /**
     * @Route("/getAreasInteres", name="getAreasInteres", methods={"GET"})
     */
    public function getAreasInteres(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getAreasInteres?IdiomaId=es
        dd($this->soap->getAreasInteres($request));
    }
    /**
     * @Route("/getOficinas", name="getOficinas", methods={"GET"})
     */
    public function getOficinas(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getOficinas?OficinaId=WALC

        dd($this->soap->getOficinas($request));
    }
    /**
     * @Route("/getSecretarias", name="getSecretarias", methods={"GET"})
     */
    public function getSecretarias(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSecretarias?Iniciales=JH

        dd($this->soap->getSecretarias($request));
    }
    /**
     * @Route("/getResponsablesMarketing", name="getResponsablesMarketing", methods={"GET"})
     */
    public function getResponsablesMarketing(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getResponsablesMarketing?Iniciales=LCM

        dd($this->soap->getResponsablesMarketing($request));
    }
    /**
     * @Route("/getSociosResponsables", name="getSociosResponsables", methods={"GET"})
     */
    public function getSociosResponsables(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getSociosResponsables?Iniciales=JCV

        dd($this->soap->getSociosResponsables($request));
    }
    /**
     * @Route("/getContactoWebtForm", name="getContactoWebtForm", methods={"GET"})
     */
    public function getContactoWebtForm(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getContactoWebtForm?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->getContactoWebtForm($request));
    }
    /**
     * @Route("/createEventoForGestionEventos", name="createEventoForGestionEventos", methods={"GET","POST"})
     */
    public function createEventoForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createEventoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->createEventoForGestionEventos($request));
    }
    /**
     * @Route("/updateEventoForGestionEventos", name="updateEventoForGestionEventos", methods={"GET","POST"})
     */
    public function updateEventoForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/updateEventoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->updateEventoForGestionEventos($request));
    }

    /**
     * @Route("/deleteEventoForGestionEventos", name="deleteEventoForGestionEventos", methods={"GET"})
     */
    public function deleteEventoForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/deleteEventoForGestionEventos?DeletedShortName=EXT4&IdEventoWeb=19169

        dd($this->soap->deleteEventoForGestionEventos($request));
    }
    /**
     * @Route("/getEventosForGestionEventos", name="getEventosForGestionEventos", methods={"GET"})
     */
    public function getEventosForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/getEventosForGestionEventos?GetFullData=1&Id=20171&IdEventoWeb=19168

        dd($this->soap->getEventosForGestionEventos($request));
    }
    /**
     * @Route("/createContactoForGestionEventos", name="createContactoForGestionEventos", methods={"GET","POST"})
     */
    public function createContactoForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createContactoForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->createContactoForGestionEventos($request));
    }
    
    /**
     * @Route("/createEventoAsistenteForGestionEventos", name="createEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function createEventoAsistenteForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/createEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->createEventoAsistenteForGestionEventos($request));
    }

    /**
     * @Route("/confirmAttendanceEventoAsistenteForGestionEventos", name="confirmAttendanceEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function confirmAttendanceEventoAsistenteForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/confirmAttendanceEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->confirmAttendanceEventoAsistenteForGestionEventos($request));
    }


    /**
     * @Route("/cancelAttendanceEventoAsistenteForGestionEventos", name="cancelAttendanceEventoAsistenteForGestionEventos", methods={"GET","POST"})
     */
    public function cancelAttendanceEventoAsistenteForGestionEventos(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/cancelAttendanceEventoAsistenteForGestionEventos?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->cancelAttendanceEventoAsistenteForGestionEventos($request));
    }

    /**
     * @Route("/addContactoWebForm", name="addContactoWebForm", methods={"GET","POST"})
     */
    public function addContactoWebForm(Request $request)
    {
        //Ejemplo http://127.0.0.1:8000/gestorcontactos/addContactoWebForm?Guid=00505693770F1EDA8B93326D22524160

        dd($this->soap->addContactoWebForm($request));
    }
}

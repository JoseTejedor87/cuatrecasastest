<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\SOAPContactsClientController;
use Symfony\Component\HttpClient\HttpClient;

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
    /**
     * @Route("/getCategorias", name="getCategorias", methods={"GET"})
     */
    public function getCategorias()
    {
        $client = HttpClient::create();
        $categorias = [
            "propiedad-intelectual" => [
                "es" => [2,234,3,4,172,38,6,7,5,291,19,8,9,10,11,12,564,1],
                "en" => [633,634,635,636,637,639,641,643,640,644,646,647,648,649,650,651,-1,560]
            ],
            "competencia" => [
                "es" => [256,200,48,201,715,202,199],
                "en" => [-1,236,482,237,-1,238,240,233]
            ],
            "deporte-entretenimiento" =>[
                "es" => [224,20,13,12,15,3,2,19,16,33,1,14],
                "en" => [239,161,162,163,164,165,166,167,168,169,158]
            ],
            "mercado-de-valores" => [
                "es" => [1045,1092,1081,1090,1044,1110,1107,1109,1111,1086,1108,1,1106],
                "en" => [1123,1103,1097,1096,1076,1116,1113,1115,1117,1094,1114,560,1112]
            ],
            "laboral" => [
                "es" => [120,142,285,133,334,341,2,419,599,3,103,122,633,613,144,155,190,349,833,601,10,64,108,206,131,219,611,609,211,847,212,625,143,615,191,4,161,603,226,239,101,132,5,121,492,130,637,605,621,342,631,6,188,118,7,213,8,65,269,627,189,361,220,286,275,497,9,1,123,251,607,495,169],
                "en" => [548,549,550,551,552,-1,553,554,600,555,556,557,634,614,558,559,560,561,840,602,562,563,564,565,566,567,612,610,568,-1,569,626,570,616,571,572,573,604,574,575,576,577,578,579,-1,580,638,606,622,581,632,582,583,584,585,586,587,588,589,628,590,591,592,593,594,-1,595,545,596,597,608,-1,598]
            ]
        ];
        foreach ($categorias as $key => $categoria) {
            $categoriaKey = $key;
            foreach ($categoria as $key1 => $value) {
                $idiomaKey = $key1;
                $response =  $client->request('GET','https://blog.cuatrecasas.com/'.$categoriaKey.'/wp-json/wp/v2/categories?per_page=80&lang='.$idiomaKey);
                $content = $response->toArray();
                foreach ($content as $key2 => $value2) {
                    die(dd($value2));
                }
            }
            
        }
        // $response =  $client->request('GET','https://blog.cuatrecasas.com/propiedad-intelectual/wp-json/wp/v2/categories?per_page=50&lang=es');
        // $response =  $client->request('GET','https://blog.cuatrecasas.com/competencia/wp-json/wp/v2/categories?per_page=50&lang=en');
        // $response =  $client->request('GET','https://blog.cuatrecasas.com/deporte-entretenimiento/wp-json/wp/v2/categories?per_page=50&lang=en');
        //  $response =  $client->request('GET','https://blog.cuatrecasas.com/mercado-de-valores/wp-json/wp/v2/categories?per_page=50&lang=es');
        // $response =  $client->request('GET','https://blog.cuatrecasas.com/laboral/wp-json/wp/v2/categories?per_page=80&lang=es');
        // $content = $response->toArray();
        // die(var_dump($content[0]));
        // $idpropiedadIntelectuales = [2,234,3,4,172,38,6,7,5,291,19,8,9,10,11,12,564,1];
        // $idpropiedadIntelectualen = [633,634,635,636,637,639,641,643,640,644,646,647,648,649,650,651,X(564),560];

        // $idcompetenciaes = [256,200,48,201,715,202,199];
        // $idcompetenciaen = [X(256),236,482,237,X(715),238,240,233];

        // $iddeportees = [224,20,13,12,15,3,2,19,16,33,1,14];
        // $iddeporteen = [239,161,162,163,164,165,166,167,168,169,158];

        // $idmercadoes = [1045,1092,1081,1090,1044,1110,1107,1109,1111,1086,1108,1,1106];
        // $idmercadoen = [1123,1103,1097,1096,1076,1116,1113,1115,1117,1094,1114,560,1112];

        // $idlaborales = [120,142,285,133,334,341,2,419,599,3,103,122,633,613,144,155,190,349,833,601,10,64,108,206,131,219,611,609,211,847,212,625,143,615,191,4,161,603,226,239,101,132,5,121,492,130,637,605,621,342,631,6,188,118,7,213,8,65,269,627,189,361,220,286,275,497,9,1,123,251,607,495,169];
        // $idlaboralen = [548,549,550,551,552,X,553,554,600,555,556,557,634,614,558,559,560,561,840,602,562,563,564,565,566,567,612,610,568,X,569,626,570,616,571,572,573,604,574,575,576,577,578,579,X,580,638,606,622,581,632,582,583,584,585,586,587,588,589,628,590,591,592,593,594,X,595,545,596,597,608,X,598];


    }
    /**
     * @Route("/getCategoriasPost", name="getCategoriasPost", methods={"GET"})
     */
    public function getCategoriasPost()
    {
        $client = HttpClient::create();
        // $response =  $client->request('GET','https://blog.cuatrecasas.com/propiedad-intelectual/wp-json/wp/v2/posts?categories=2');
        // $content = $response->toArray();
        // dd($content);
        $response1 =  $client->request('GET','https://blog.cuatrecasas.com/propiedad-intelectual/wp-json/wp/v2/posts?categories=633&lang=en');
        $content1 = $response1->toArray();
        dd($content1);

    }
}

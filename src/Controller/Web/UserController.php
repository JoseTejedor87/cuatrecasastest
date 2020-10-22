<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\EventTranslationRepository;
use App\Repository\AwardRepository;
use App\Repository\PublicationRepository;
use App\Repository\BannerRepository;
use App\Repository\SliderRepository;
use App\Repository\HomeRepository;
use App\Repository\GeneralBlockRepository;
use App\Controller\Web\WebController;
use App\Repository\SOAPContactsClientRepository;
use App\Controller\SOAPContactsClientController;


class UserController extends WebController
{
    private $soap;


    public function __construct()
    {
        $this->soap  = new SOAPContactsClientController;
        
    }
    public function newUser(Request $request,SOAPContactsClientRepository $SOAPContactsClientRepository)
    {
        $paises = $SOAPContactsClientRepository->getPaises();
        $provincias = $SOAPContactsClientRepository->getProvincias();
        $idiomas = $SOAPContactsClientRepository->getIdiomas();
        $areasinteres = $SOAPContactsClientRepository->getAreasInteres();
        $Guid = $request->query->get('Guid');
        if($Guid){
            $ContacRespon = $this->soap->getContactoWebtForm($Guid);
            $contacto = json_decode($ContacRespon->getContent());
        }


        return $this->render('web/home/userSettings.html.twig', [
            'paises' => $paises,
            'provincias' => $provincias,
            'idiomas' => $idiomas,
            'areasinteres' => $areasinteres,
            'contacto' => isset($contacto) ? $contacto : '',
            'controller_name' => 'NewUser'
        ]);
    }
    public function ajaxActionContact(Request $request)    
    {
        $fecha = new \DateTime();
        $contacto = $request->query->get('contacto');
        $contactoA = json_decode($contacto, true);

        $contactoA['Accion']=1;
        $contactoA['EstadoContacto']=2;
        $contactoA['FechaModificacion']=$fecha->format('Y-m-d\TH:i:s');
        $contactoA['CreatedShortName']='';
        $contactoA['Guid']='';
        $contactoA['IdAccount']='';
        $contactoA['IdContacto']='';
        $contactoA['IdEstadoEnvioWeb']='';
        $contactoA['IdOrigenContacto']="Newsletter_prensa";
        $contactoA['MotivoBaja']="";
        $contactoReturn = $this->soap->addContactoWebForm(array('contactoWebDto'=>($contactoA)));
        return new JsonResponse($contactoReturn);
    }

   
}

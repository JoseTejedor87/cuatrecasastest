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


        return $this->render('web/home/userSettings.html.twig', [
            'paises' => $paises,
            'provincias' => $provincias,
            'idiomas' => $idiomas,
            'controller_name' => 'NewUser'
        ]);
    }
    public function ajaxActionContact(Request $request)    
    {

        $contacto = $request->query->get('contacto');
        
        $contactoA = json_decode($contacto, true);
        dd($contactoA);
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

   
}

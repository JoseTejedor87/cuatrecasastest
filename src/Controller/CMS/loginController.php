<?php

namespace App\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class loginController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     */
    public function index(Request $request)
    {
        $client = new \SoapClient('http://srvwebinta.cuatrecasas.com/VSNET/WebServicesCGP/Credentials/Service.svc?wsdl', array("trace"=>1, "exception" => 0));
        $res  = $client->ValidateSSO( array ('input' =>( array ( 'Data' => 'Qsi4c0Dy0dASSyoyGg9UY0/9sSF+aAVE67kPtm0+LJZfrlvTXI8eZg7Az+c4q6MgPJ1PKiYUARZLt05GcSl38QlV1MeSZZJGNUsHklzTatlwg3Nwr85AzVnGfDtOWAON', 'SSOType'  =>  'SSO'))));
        // dump($client->__getTypes());
        // dump($client->__getFunctions());
        // dump($client->__getLastRequest());
        // dump($client->__getLastRequestHeaders());

        $data = $res->ValidateSSOResult->Data;
        $result = $res->ValidateSSOResult->Result;
        if($result){
            $session = new Session();
            $session->start();
            $session->set('User', $data);
            return $this->redirect('/dashboard');
        }else{
            $session->remove('User');
            // return $this->render('cms/login/index.html.twig', [
            //     'controller_name' => 'loginController',
            // ]);
        }
    }
}

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
        $res  = $client->ValidateSSO( array ('input' =>( array ( 'Data' => '8d6fudhBZeS/MRZVlw+2otLgws7IHvSkQ9pqSO9tjTeEB+hImdCJFzbrwiM76COMWmEsDfuRzBzMdYs1jlccEJP16uIFyLmXh0OB0o/EzJH9zmZXv8pCpxDscUAnp5Ov', 'SSOType'  =>  'SSO'))));
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
            if($session)
            $session->remove('User');

        }

            // return $this->render('cms/login/index.html.twig', [
            //     'controller_name' => 'loginController',
            // ]);
    }

    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}

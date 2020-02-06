<?php

namespace App\Controller\Web;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class WebController extends AbstractController
{
    public function isThisLocale(Request $request,$locale ){
        if($request->getLocale()){
            if($request->getLocale() != $locale){
                $request->setLocale($locale);
            }
        }
    }

}

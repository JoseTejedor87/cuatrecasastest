<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\RequestStack;

class NavigationService
{
    protected $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getLanguage()
    {
        $params = $this->request->attributes->get('_route_params');
        return $params['_locale'];
    }

    public function getRegion()
    {
        $params = $this->request->attributes->get('_route_params');
        return $params['_region'];
    }
}

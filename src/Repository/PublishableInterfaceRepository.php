<?php

namespace App\Repository;

use Symfony\Component\HttpFoundation\Request;

interface PublishableInterfaceRepository
{
    public function getInstanceByRequest(Request $request);
}

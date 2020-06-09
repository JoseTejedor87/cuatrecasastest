<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sector;
use App\Repository\SectorRepository;
use App\Repository\ActivityRepository;
use App\Repository\ActivityTranslationRepository;
use App\Controller\Web\WebController;

class ActivityController extends WebController
{
    public function successStories()
    {
        return $this->render('web/activity/successStories.html.twig', [
            'controller_name' => 'ActivityController',
        ]);
    }
}

<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebController extends AbstractController
{

    protected function getPhotoPathByFilter($publication, $filter,$navigation)
    {
        if ($photos = $publication->getAttachments()) {
            foreach ($photos as $key => $photo) {
                if ($photo->isPublished($navigation->getLanguage(),$navigation->getRegion())){
                    if ($photo->getType() == "publication_main_photo" ) {
                        $photo = $this->imagineCacheManager->getBrowserPath(
                            '/resources/' . $photo->getFileName(),
                            $filter
                        );
                        return $photo;
                    }
                }
            }
        }
    }

    protected function getDossierPathPublished($dossiers, $navigation)
    {
        $array_Dossiers = [];
        if ($dossiers = $publication->getAttachments()) {
            foreach ($dossiers as $key => $dossier) {
                if ($dossier->isPublished($navigation->getLanguage(),$navigation->getRegion())){
                    if ($dossier->getType() == "publication_dossier" ) {
                        $dossier = '/resources/' . $dossier->getFileName();
                        /*
                        $dossier = $this->imagineCacheManager->getBrowserPath(
                            '/resources/' . $dossier->getFileName(),
                            $filter
                        );*/
                        array_push($array_Dossiers, $dossier);
                    }
                }
            }
        }
        return $array_Dossiers;
    }

}

<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\HttpException;

use App\Repository\PublicationRepository;
use App\Controller\Web\NavigationService;


class APIController extends AbstractFOSRestController
{
    public function posts(Request $request, publicationRepository $publicationRepository, NavigationService $NavigationService)
    {
        $query = $publicationRepository->createQueryBuilder('p');
        /////////////////////////////////////////
        //Ahora mismo estamos buscando por NEWS Y OPINION, pero cuando importemos los post tenemos que cambiarlo 
        /////////////////////////////////// 
        $query = $query->orWhere('p INSTANCE OF App\Entity\News');
        $query = $query->orWhere('p INSTANCE OF App\Entity\Opinion');
        $query = $query->getQuery();
        $publications = $query->getResult();
        $posts = array();
        $lang = 'es';
        $json = [
            "id"   => "",
            "date"   => "",
            "modified"    => "",
            "slug"   => "",
            "link"   => "",
            "title"    => "",
            "content"   => "",
            "excerpt"   => "",
            "author"    => "",
            "featured_media"    => ""
        ];
        foreach ($publications as $key => $Opinion) {
            $authors = array();
            $attachments = array();
            $json['id'] = $Opinion->getId();
            $json['date'] = $Opinion->getCreatedAt()->format('Y-m-d\TH:i:s.uP');
            $json['modified'] = $Opinion->getUpdatedAt()->format('Y-m-d\TH:i:s.uP');
            $json['slug'] = $Opinion->translate($lang)->getSlug();
            $json['link'] = $NavigationService->getPathByPublishable($Opinion);
            $json['title'] = $Opinion->translate($lang)->getTitle();
            $json['content'] = $Opinion->translate($lang)->getContent();
            $json['excerpt'] = $Opinion->translate($lang)->getSummary();
            if($Opinion->getPeople()){
                foreach ($Opinion->getPeople() as $key1 => $people) {
                   array_push($authors,$people->getId());
                }
            }
            if($Opinion->getAttachments()){
                foreach ($Opinion->getAttachments() as $key1 => $attachment) {
                   array_push($attachments,$attachment->getId());
                }
            }
            $json['author'] =implode(",", $authors); 
            $json['featured_media'] = implode(",", $attachments); 
            array_push($posts,$json);
        }
        return new JsonResponse($posts);
    }
    public function media(Request $request, publicationRepository $publicationRepository, NavigationService $NavigationService)
    {
        $query = $publicationRepository->createQueryBuilder('p');
        /////////////////////////////////////////
        //Ahora mismo estamos buscando por NEWS Y OPINION, pero cuando importemos los post tenemos que cambiarlo 
        /////////////////////////////////// 
        $query = $query->orWhere('p INSTANCE OF App\Entity\News');
        $query = $query->orWhere('p INSTANCE OF App\Entity\Opinion');
        $query = $query->getQuery();
        $publications = $query->getResult();
        $attachmentA = array();
        $lang = 'es';
        $json = [
            "id"   => "",
            "date"   => "",
            "guid"   => "",
            "modified"    => "",
            "slug"   => "",
            "type"   => "",
            "title"    => "",
            "media_type"   => "",
            "mime_type"   => "",
            "media_details"    => ""
        ];
        foreach ($publications as $key => $Opinion) {
            if($Opinion->getAttachments()){
                foreach ($Opinion->getAttachments() as $key1 => $attachment) {
                    $json['id'] = $attachment->getId();
                    $json['date'] = $attachment->getCreatedAt()->format('Y-m-d\TH:i:s.uP');
                    $json['guid'] = ["rendered" => $attachment->getFile()->getPathname()];
                    $json['modified'] = $attachment->getUpdatedAt()->format('Y-m-d\TH:i:s.uP');
                    $json['slug'] = $attachment->getFile()->getPathname();
                    $json['type'] = $attachment->getType();
                    $json['title'] = ["rendered" => $attachment->getTitle()];
                    $json['media_type'] = $attachment->getFile()->getExtension();
                    $json['media_details'] = ["file" => $attachment->getFileName()];
                    array_push($attachmentA,$json);
                }
            }
        }
        dd($attachmentA);
        return new JsonResponse($attachmentA);
    }
}

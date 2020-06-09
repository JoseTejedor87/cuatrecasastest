<?php

namespace App\Controller\Web;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NavigationService
{
    protected $request;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
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

    public function getPathByPublishable($publishable)
    {
        $path = '';
        if ($publishable instanceof \App\Entity\Article) {
            $path = $this->router->generate('articles_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Desk) {
            $path = $this->router->generate('desks_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Event) {
            $path = $this->router->generate('events_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Insight) {
            $path = $this->router->generate('insights_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Lawyer) {
            $path = $this->router->generate('lawyers_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Office) {
            $path = $this->router->generate('offices_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Page) {
            $path = $this->router->generate('pages_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Practice) {
            $path = $this->router->generate('practices_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Product) {
            $path = $this->router->generate('products_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Resource) {
            $path = $this->router->generate('resources_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        } elseif ($publishable instanceof \App\Entity\Sector) {
            $path = $this->router->generate('sectors_detail', [
                'slug' => $publishable->translate()->getSlug()
            ]);
        }

        return $path;
    }
}

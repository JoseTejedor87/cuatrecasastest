<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PublishableService
{
    protected $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function path($publishable)
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

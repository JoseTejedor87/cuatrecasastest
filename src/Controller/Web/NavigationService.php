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

    public function getHrefLang($language, $region)
    {
        $hreflangs = [];
        $hreflangs['es']['spain'] = 'es-es';
        $hreflangs['es']['latam'] = 'es';
        $hreflangs['en']['spain'] = 'en-es';
        $hreflangs['en']['latam'] = 'en';

        return $hreflangs[$language][$region] ?? '';
    }

    public function getAlternativePath($language = null, $region = null)
    {
        $language = $language ?? $this->getLanguage();
        $region = $region ?? $this->getRegion();

        // if there is a publishable instance in the current request,
        // use "getPathByPublishable" to obtain the custom path
        // defined for the current published instance. In other case
        // just rewrite the current request using the $language and
        // $region parameters

        if ($publishable = $this->router->getContext()->getParameter('_publishable')) {
            // Calling getPathByPublishable to retrieve the custom path
            // for the current $publishable instance
            $path = $this->getPathByPublishable($publishable, $language, $region);
        } else {
            // Rewriting the current request using
            // the $language and $region parameters
            $pathName = $this->request->get('_route');
            $path = $this->router->generate(
                $pathName,
                [
                    '_locale' => $language,
                    '_region' => $region
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }
        return $path;
    }

    // Obtain the custom path for an specific publishable instance.
    // The optional parameters $language and $region could be used to force some values.
    // If no optional parameter received, the method use the values from the current request.
    public function getPathByPublishable($publishable, $language = null, $region = null)
    {
        $path = null;
        $params = [];

        $language = $language ?? $this->router->getContext()->getParameter('_locale');
        $region = $region ?? $this->router->getContext()->getParameter('_region');

        $params['_locale'] = $language;
        $params['_region'] = $region;

        // To avoid the path calculation of a not publishable instances and unpublished instances
        if (($publishable instanceof \App\Entity\Publishable) && $publishable->isPublished($language, $region)) {
            if ($publishable instanceof \App\Entity\Article) {
                $pathName = 'articles_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Desk) {
                $pathName = 'desks_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Event) {
                $pathName = 'events_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Insight) {
                $pathName = 'insights_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Lawyer) {
                $pathName = 'lawyers_detail';
                $params['slug'] = $publishable->getSlug();
            } elseif ($publishable instanceof \App\Entity\Office) {
                $pathName = 'offices_detail';
                $params['slug'] = $publishable->getSlug();
            } elseif ($publishable instanceof \App\Entity\Page) {
                $pathName = 'pages_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Practice) {
                $pathName = 'practices_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Product) {
                $pathName = 'products_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Resource) {
                $pathName = 'resources_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            } elseif ($publishable instanceof \App\Entity\Sector) {
                $pathName = 'sectors_detail';
                $params['slug'] = $publishable->translate($language)->getSlug();
            }

            $path = $this->router->generate($pathName, $params, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $path;
    }
}

<?php

namespace App\Controller\EventListener;

use App\Repository\EventRepository;
use App\Repository\LawyerRepository;
use App\Repository\DeskRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Intl\Languages;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Web\NavigationService;

class SitemapSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param EventRepository    $eventRepository
     * @param LawyerRepository    $lawyerRepository
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        NavigationService $NavigationService,
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        LawyerRepository $lawyerRepository,
        DeskRepository $deskRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->lawyerRepository = $lawyerRepository;
        $this->NavigationService = $NavigationService;
        $this->deskRepository = $deskRepository;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::ON_SITEMAP_POPULATE => 'populate',
        ];
    }

    /**
     * @param SitemapPopulateEvent $sevent
     */
    public function populate(SitemapPopulateEvent $sevent): void
    {
        $languages = array('es', 'en', 'pt', 'ge');
        foreach ($languages as $language) {
            $this->registerPostsUrls($sevent->getUrlContainer(), $language);
        }
    }

    /**
     * @param UrlContainerInterface $urls
     */
    public function registerPostsUrls(UrlContainerInterface $urls, $language): void
    {
        $events = $this->eventRepository->findAll();
        $lawyers = $this->lawyerRepository->findAll();
        $desks = $this->deskRepository->findAll();





        // Events
        foreach ($events as $event) {
            if ($event->translate($language)->getSlug() !== "" && $event->translate($language)->getSlug() !== null) {
                if (!empty($event->getRegions())) {
                    foreach ($event->getRegions() as $region) {
                        $url = $this->NavigationService->getPathByPublishable($event, $language, $region);
                        if ($url !== null) {
                            $urls->addUrl(
                                new UrlConcrete($url),
                                $language
                            );
                        }
                    }
                }
            }
        }
        // Lawyers
        foreach ($lawyers as $lawyer) {
            if ($lawyer->getSlug() !== "" && $lawyer->getSlug() !== null) {
                if (!empty($lawyer->getRegions())) {
                    foreach ($lawyer->getRegions() as $region) {
                        $url = $this->NavigationService->getPathByPublishable($lawyer, $language, $region);
                        if ($url !== null) {
                            $urls->addUrl(
                                new UrlConcrete($url),
                                $language
                            );
                        }
                    }
                }
            }
        }
        // Desks
        foreach ($desks as $desk) {
            if ($desk->translate($language)->getSlug() !== "" && $desk->translate($language)->getSlug() !== null) {
                if (!empty($desk->getRegions())) {
                    foreach ($desk->getRegions() as $region) {
                        $url = $this->NavigationService->getPathByPublishable($desk, $language, $region);
                        if ($url !== null) {
                            $urls->addUrl(
                                new UrlConcrete($url),
                                $language
                            );
                        }
                    }
                }
            }
        }
    }
}

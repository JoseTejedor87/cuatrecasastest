<?php

namespace App\Controller\EventListener;

use App\Repository\OfficeRepository;
use App\Repository\ActivityRepository;
use App\Repository\CaseStudyRepository;
use App\Repository\InsightRepository;
use App\Repository\PageRepository;
use App\Repository\PublicationRepository;



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
        DeskRepository $deskRepository,
        OfficeRepository $officeRepository,
        ActivityRepository $activityRepository,
        CaseStudyRepository $caseStudyRepository,
        InsightRepository $insightRepository,
        PageRepository $pageRepository,
        PublicationRepository $publicationRepository
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->lawyerRepository = $lawyerRepository;
        $this->NavigationService = $NavigationService;
        $this->deskRepository = $deskRepository;

        $this->officeRepository = $officeRepository;
        $this->activityRepository = $activityRepository;
        $this->caseStudyRepository = $caseStudyRepository;
        $this->insightRepository = $insightRepository;
        $this->pageRepository = $pageRepository;
        $this->publicationRepository = $publicationRepository;
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
        $offices = $this->officeRepository->findAll();
        $activities = $this->activityRepository->findAll();
        $caseStudies = $this->caseStudyRepository->findAll();
        $insights = $this->insightRepository->findAll();
        $pages = $this->pageRepository->findAll();
        $publications = $this->publicationRepository->findAll();

        // Events
        $this->generateSlugTranslated($events, $language, $urls);
        var_dump("Events");
        // Lawyers
        $this->generateSlug($lawyers, $language, $urls);
        var_dump("Lawyers");
        // Desks
        $this->generateSlugTranslated($desks, $language, $urls);
        var_dump("Desks");
        // Offices
        $this->generateSlug($offices, $language, $urls);
        var_dump("Offices");
        // Activities
        $this->generateSlugTranslated($activities, $language, $urls);
        var_dump("Activities");
        // Case Studies
        $this->generateSlugTranslated($caseStudies, $language, $urls);
        var_dump("Studies");
        // Insights
        $this->generateSlugTranslated($insights, $language, $urls);
        var_dump("Insights");
        // Pages
        $this->generateSlugTranslated($pages, $language, $urls);
        var_dump("Pages");
        // Publications
        $this->generateSlugTranslated($publications, $language, $urls);
        var_dump("Publications");
    }

    public function generateSlugTranslated($entities, $language, $urls)
    {
        foreach ($entities as $entity) {
            if ($entity->translate($language)->getSlug() !== "" && $entity->translate($language)->getSlug() !== null) {
                if (!empty($entity->getRegions())) {
                    foreach ($entity->getRegions() as $region) {
                        $url = $this->NavigationService->getPathByPublishable($entity, $language, $region);
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
    public function generateSlug($entities, $language, $urls)
    {
        foreach ($entities as $entity) {
            if ($entity->getSlug() !== "" && $entity->getSlug() !== null) {
                if (!empty($entity->getRegions())) {
                    foreach ($entity->getRegions() as $region) {
                        $url = $this->NavigationService->getPathByPublishable($entity, $language, $region);
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

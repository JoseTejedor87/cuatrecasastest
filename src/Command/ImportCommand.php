<?php
namespace App\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Activity;
use App\Entity\Award;
use App\Entity\Desk;
use App\Entity\Event;
use App\Entity\Lawyer;
use App\Entity\Mention;
use App\Entity\Practice;
use App\Entity\Quote;
use App\Entity\Resource;
use App\Entity\Sector;
use App\Entity\Person;
use App\Entity\Office;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\LegalNovelty;
use App\Entity\Research;
use App\Entity\Opinion;
use App\Entity\News;
use App\Entity\Publication;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private $container;
    private $logger;
    private $mappedLawyerIds;
    private $mappedEventIds;
    private $mappedActivityIds;
    private $mappedOfficeIds;
    private $mappedPersonIds;

    const SOURCE_DOMAIN = "https://www.cuatrecasas.com";

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct();
        $this->container = $container;
        $this->logger = $logger;

        $this->em = $this->container->get('doctrine')->getManager();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setHelp("Este comando sirve para migrar de la BBDD Web cuatrecasas Cms a web cuatrecasas de la nueva web")
            // the full command description shown when running the command with
            ->setDescription('Este comando sirve para migrar de la BBDD Web cuatrecasas Cms a web cuatrecasas de la nueva web')
            // Set options
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('table', 'a', InputOption::VALUE_REQUIRED, "Si table esta vacio, se ejecutará para todas las tablas", "all"),
                ))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Empezando la migración...');
        $table = $input->getOption('table');

        if ($table=="all") {
            $this->logger->info("Se van a importar todas las tablas");
            $this->Lawyers();
            $this->Events();
            $this->Activities();
            $this->PeopleByEvent();
            $this->ActivitiesByEvent();
            $this->ActivitiesByLawyer();
            $this->Quote();
            $this->Office();
            $this->OfficeByLawyer();
            $this->awards();
            $this->Article();
            $this->ArticlesByActivities();
            $this->ArticlesByOffices();
            $this->ArticlesByLawyers();
        } else {
            switch ($table) {
                case "lawyer":
                    $this->Lawyers();
                    break;
                case "event":
                    $this->Events();
                    break;
                case "activity":
                    $this->Activities();
                    break;
                case "PeopleByEvent":
                    $this->PeopleByEvent();
                    break;
                case "event_activity":
                    $this->ActivitiesByEvent();
                    break;
                case "lawyer_activity":
                    $this->ActivitiesByLawyer();
                    break;
                case "quote":
                    $this->Quote();
                    break;
                case "office":
                    $this->Office();
                    break;
                case "OfficeByLawyer":
                    $this->OfficeByLawyer();
                    break;
                case "OfficeByEvents":
                    $this->OfficeByEvents();
                    break;
                case "awards":
                    $this->awards();
                    break;
                case "publication":
                    $this->Publications();
                    break;
                case "publicationsByLawyers":
                    $this->PublicationsByLawyers();
                    break;
                case "publicationsByOffices":
                    $this->PublicationsByOffices();
                    break;
                case "publicationsByActivities":
                    $this->PublicationsByActivities();
                    break;
                case "ArticlesCategory":
                    $this->ArticlesCategory();
                    break;
                case "ArticlesAuthors":
                    $this->ArticlesAuthors();
                    break;
                case "ArticlesPost":
                    $this->ArticlesPost();
                    break;
                case "ArticlesPostFiles":
                    $this->ArticlesPostFiles();
                    break;
                case "ActivityActivities":
                    $this->ActivityActivities();
                    break;
                case "news":
                    $this->News();
                    break;
                case "newsByLawyers":
                    $this->NewsByLawyers();
                    break;
                case "newsByOffices":
                    $this->NewsByOffices();
                    break;
                case "newsByActivities":
                    $this->NewsByActivities();
                    break;
                case "regenerateSlugPublication":
                    $this->regenerateSlugPublication();
                    break;
            }
        }
        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function Lawyers()
    {
        $data = file_get_contents("JsonExports/abogados.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/lawyer-*"));

        // Removing registers from database
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Lawyer], RESEED, 1)");
        // $this->em->getConnection()->executeQuery("DELETE FROM [Office] ");
        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE lawyer_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [article_person]");
        $this->em->getConnection()->executeQuery("DELETE FROM [event_person]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Person]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Person], RESEED, 1)");
        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_secondary_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [LawyerTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Lawyer]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Lawyer], RESEED, 1)");

        $processedLawyersMap = [];

        foreach ($items as $item) {
            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if ($item['status']=='0') {
                continue;
            }

            $oldLawyerId = $item['id_abogado'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current lawyer instance in a previous iteration ?
            if (isset($processedLawyersMap[$oldLawyerId])) {
                // in that case, restore it from $processedLawyersMap
                $lawyer = $processedLawyersMap[$oldLawyerId];
            } else {
                // in other case, create a new instance and fill it
                $lawyer = new Lawyer();
                $lawyer->setOldId($oldLawyerId);
                $lawyer->setName($item['nombre']);
                $lawyer->setSurname($item['apellidos']);
                $lawyer->setEmail($item['email']);
                $lawyer->setPhone(($item['telefono']));
                $lawyer->setFax(($item['fax']));

                if ($item['image']) {
                    // normalizing image paths
                    $path = $item['image'];
                    $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                    $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                    $path = self::SOURCE_DOMAIN.$path;
                    // Importing image from source
                    $photo = $this->importFile('lawyer', $path);
                    if ($photo) {
                        $resource = new Resource();
                        $resource->setFile($photo);
                        $resource->setFileName($photo->getFileName());
                        $resource->setLawyer($lawyer);
                        $lawyer->setPhoto($resource);
                    }
                }

                $lawyer->setLawyerType(
                    self::getMappedLawyerType($item['idtipoabogado'])
                );
            }
            // Updating the languages field using the correspondent visio_* field
            $languages = [];
            foreach (['esp','por','eng','chi'] as $lang) {
                if ($item['visio_'.$lang] == "1") {
                    $languages[] = self::getMappedLanguageCode($lang);
                }
            }
            $lawyer->setLanguages($languages);

            // Filling translatable fields
            $lawyer->translate($currentLang)->setDescription($item['descripcion']);
            $lawyer->translate($currentLang)->setCurriculum($item['CV']);
            $Training = $item['formacion'];
            if($Training){
                if(preg_match('/Languages/',$Training)){
                    $matches = explode("Languages", $Training);
                }else{
                    if(preg_match('/Language/',$Training)){
                        $matches = explode("Language", $Training);
                    }
                }
                if(preg_match('/Idiomas/',$Training)){
                    $matches = explode("Idiomas", $Training);
                }else{
                    if(preg_match('/Idioma/',$Training)){
                        $matches = explode("Idioma", $Training);
                    }
                }
                if(isset($matches)){
                    $delimiter = array(" y ", " e ", " and ");
                    $languages = str_replace($delimiter, " , ", strip_tags($matches[1]));
                    $lawyer->translate($currentLang)->setTraining($matches[0]);
                    $languages = str_replace(": ", "", $languages);
                    $languageA = explode(",", $languages);
                    foreach ($languageA as $languagekey => $language) {
                        $lawyer->translate($currentLang)->setLanguagesLawyer(
                            array_unique(
                                array_merge($lawyer->translate($currentLang)->getLanguagesLawyer() ? $lawyer->translate($currentLang)->getLanguagesLawyer() : [], [$language])
                            )
                        );
                    }
                }
            }
            // $lawyer->translate($currentLang)->setTraining($item['formacion']);
            $lawyer->translate($currentLang)->setMentions($item['menciones']);
            // Adding the current instance to map
            $processedLawyersMap[$oldLawyerId] = $lawyer;
        }

        foreach ($processedLawyersMap as $lawyer) {
            $lawyer->mergeNewTranslations();
            $this->em->persist($lawyer);
            $this->em->flush();
            $this->logger->debug("Lawyer ".$lawyer->getOldId()." - ".$lawyer->getId()." ".$lawyer->getFullName());
        }
    }


    public function Events()
    {
        $data = file_get_contents("JsonExports/eventos.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/event-*"));

        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE event_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [event_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [event_person]");
        $this->em->getConnection()->executeQuery("DELETE FROM [EventTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Event]");

        $processedEventsMap = [];
        $processedAttachmentsMap = [];

        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if ($item['status']=='0' || $item['visible']=='0' || empty($item['titulo'])) {
                continue;
            }

            $oldEventId = $item['id'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current event instance in a previous iteration ?
            if (isset($processedEventsMap[$oldEventId])) {
                // in that case, restore it from $processedEventsMap
                $event = $processedEventsMap[$oldEventId];
            } else {
                // in other case, create a new instance and fill it
                $event = new Event();
                $event->setOldId($oldEventId);
                $startDate = \DateTime::createFromFormat('Y-m-d G:i:s.u', $item['fecha_inicio']);
                $event->setStartDate(
                    $startDate ? $startDate : date("Y-m-d H:i:s")
                );
                $endDate = \DateTime::createFromFormat('Y-m-d G:i:s.u', $item['fecha_final']);
                $event->setEndDate(
                    $endDate ? $endDate : date("Y-m-d H:i:s")
                );

                $event->setCustomMap($item['mapa']);
                $event->setCustomSignup($item['url_inscripcion']);
                $event->setPhone($item['telefono']);
                $event->setContact($item['contacto']);
                $event->setEventType(
                    self::getMappedEventTypeCode($item['tipo'])
                );
                $event->setCapacity($item['aforo']);
            }
            // Updating the languages field using the correspondent visio_* field
            if ($item['visio_'.$item['lang']] == "1") {
                $event->setLanguages(
                    array_unique(
                        array_merge($event->getLanguages(), [$currentLang])
                    )
                );
            }
            // Importing attachments from source
            if ($item['url_pdf']) {
                // Was processed the current attachment instance in a previous iteration ?
                if (isset($processedAttachmentsMap[$oldEventId][$item['url_pdf']])) {
                    $resource = $processedAttachmentsMap[$oldEventId][$item['url_pdf']];
                    $resource->setLanguages(
                        array_unique(
                            array_merge($resource->getLanguages(), [$currentLang])
                        )
                    );
                } else {
                    // normalizing the attachment path
                    $path = $item['url_pdf'];
                    $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                    $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                    $path = self::SOURCE_DOMAIN.$path;

                    $attachment = $this->importFile('event', $path);
                    if ($attachment) {
                        $resource = new Resource();
                        $resource->setFile($attachment);
                        $resource->setFileName($attachment->getFileName());
                        $resource->setLanguages([$currentLang]);
                        // Adding the current attachment to the attachments mapping
                        $processedAttachmentsMap[$oldEventId][$item['url_pdf']] = $resource;
                    }
                }
            }
            // Filling translatable fields
            $event->translate($currentLang)->setTitle($item['titulo']);
            $event->translate($currentLang)->setDescription($item['resumen']);
            $event->translate($currentLang)->setSchedule($item['descripcion_lugar']);
            $event->translate($currentLang)->setProgram($item['programa']);
            $event->translate($currentLang)->setCustomCity($item['ciudad']);
            $event->translate($currentLang)->setCustomAddress($item['ubicacion_lugar']);
            // Adding the current instance to the events mapping
            $processedEventsMap[$oldEventId] = $event;
        }

        foreach ($processedEventsMap as $event) {
            // Persist only the registers with at least one active language
            if (!empty($event->getLanguages())) {
                // Persist the instance
                $event->mergeNewTranslations();
                // Adding attachments to each event
                // using the unique collection $processedAttachmentsMap
                if (isset($processedAttachmentsMap[$event->getOldId()])) {
                    foreach ($processedAttachmentsMap[$event->getOldId()] as $key => $resource) {
                        $event->addAttachment($resource);
                    }
                }
                $this->em->persist($event);
                $this->em->flush();
                $this->logger->debug("Event ".$event->getId()." ".$event->translate('es')->getTitle());
            }
        }
    }

    public function Activities()
    {
        $data = file_get_contents("JsonExports/areas_practicas.json");
        $items = json_decode($data, true);

        $this->em->getConnection()->executeQuery("DELETE FROM [activity_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [activity_activity_parents]");
        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_secondary_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [event_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [ActivityTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Activity]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Activity], RESEED, 1)");

        $processedActivitiesMap = [];

        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if (empty($item['titulo'])) {
                continue;
            }

            $oldActivityId = $item['id'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current activity instance in a previous iteration ?
            if (isset($processedActivitiesMap[$oldActivityId])) {
                // in that case, restore it from $processedEventsMap
                $activity = $processedActivitiesMap[$oldActivityId];
            } else {
                // in other case, create a new instance and fill it
                // Using a subclass of Activity using id_area as reference
                if ($item['id_area']==1) {
                    $activity = new Practice();
                } elseif ($item['id_area']==2) {
                    $activity = new Sector();
                } elseif ($item['id_area']==3) {
                    $activity = new Desk();
                } else {
                    continue;
                }

                $activity->setOldId($oldActivityId);
                $activity->setHighlighted(!(bool)$item['spractica']);
            }

            // Updating the languages field using the correspondent visio_* field
            // MIRAR LOS VISIO_GER
            if ($item['lang'] == 'esp' || $item['lang'] == 'eng' || $item['lang'] == 'por' || $item['lang'] == 'chi') {
                if ($item['visio_'.$item['lang']] == "1") {
                    $activity->setLanguages(
                        array_unique(
                            array_merge($activity->getLanguages(), [$currentLang])
                        )
                    );
                }
            }
            // Filling translatable fields
            $activity->translate($currentLang)->setTitle($item['titulo']);
            // $description = $item['descripcion'] . "<br/><br/>" . $item['experiencia'];
            // $activity->translate($currentLang)->setDescription($description);
            $activity->translate($currentLang)->setSummary($item['descripcion']);
            $activity->translate($currentLang)->setDescription($item['experiencia']);

            // Adding the current instance to map
            $processedActivitiesMap[$oldActivityId] = $activity;
        }

        foreach ($processedActivitiesMap as $activity) {
            // Persist only the registers with at least one active language
            if (!empty($activity->getLanguages())) {
                // Persist the instance
                $activity->mergeNewTranslations();
                $this->em->persist($activity);
                $this->em->flush();
                $this->logger->debug("Activity ".$activity->getId()." ".$activity->translate('es')->getTitle());
            }
        }
    }
    public function ActivityActivities()
    {
        $data = file_get_contents("JsonExports/areas_relacionades.json");
        $items = json_decode($data, true);

        $this->em->getConnection()->executeQuery("DELETE FROM [activity_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [activity_activity_parents]");
        
        $activityRepository = $this->em->getRepository(Activity::class);
        foreach ($items as $item) {
            $activityId = $this->getMappedActivityId($item['id_area_padre']);
            $activityhijoId  = $this->getMappedActivityId($item['id_area_hija']);
            if ($activityId && $activityhijoId) {
                $activity = $activityRepository->find($activityId);
                $activityhijo = $activityRepository->find($activityhijoId);
                if (get_class($activity) == get_class($activityhijo) && !$activityhijo->getHighlighted()) {
                    $activity->addChild($activityhijo);
                } else {
                    $activity->addRelatedActivity($activityhijo);
                }
                $activity->addRelatedActivity($activityhijo);
                $this->em->persist($activity);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!! OLD_PARENT_ID : " . $item['id_area_padre'] . " OLD_CHILD_ID : " . $item['id_area_hija']);
            }
        }
    }
    public function ActivityupdateDescription()
    {
        $data = file_get_contents("JsonExports/areas_practicas.json");
        $items = json_decode($data, true);
        $activityRepository = $this->em->getRepository(Activity::class);
        foreach ($items as $item) {
            $activityId = $this->getMappedActivityId($item['id']);
            $currentLang = self::getMappedLanguageCode($item['lang']);
            if ($activityId) {
                $activity = $activityRepository->find($activityId);
                $activity->translate($currentLang)->setSummary($item['descripcion']);
                $activity->translate($currentLang)->setDescription($item['experiencia']);
                $this->em->persist($activity);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function Quote()
    {
        $data = file_get_contents("JsonExports/areasQuotes.json");
        $items = json_decode($data, true);

        $this->em->getConnection()->executeQuery("DELETE FROM [QuoteTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Quote]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Quote], RESEED, 1)");

        $processedQuotesMap = [];

        foreach ($items as $item) {
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if ($currentLang != 'en' || in_array($item['quote_text'], $processedQuotesMap)) {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
                continue;
            } else {
                $body = $item['quote_text'];
                $author = '';
                $year = '';
                // Trying to parse the body using a pattern
                // in order to divide data into the new fields (author, year and body)
                $matches = [];
                preg_match('/^(.*)-(.*),(.*)/', $item['quote_text'], $matches);
                if (count($matches) == 4) {
                    // Removing double quotes and white spaces
                    $body = str_replace('"', '', trim($matches[1]));
                    $author = trim($matches[2]);
                    $year = trim($matches[3]);
                }
                // Create a new quote instance and fill it
                $quote = new Quote();
                $quote->setOldId($item['id_quote']);
                $quote->setAuthor($author);
                $quote->setYear($year);

                // Filling translatable fields
                foreach (['es','en','pt','zh'] as $lang) {
                    $quote->translate($lang)->setBody($body);
                }

                // Persist the instance
                $quote->mergeNewTranslations();
                $this->em->persist($quote);
                $this->em->flush();

                $this->logger->debug("Quote ".$quote->getId()." ".substr($quote->translate('en')->getBody(), 0, 50)."...");

                $processedQuotesMap[] = $item['quote_text'];
            }
        }
    }

    public function ActivitiesByLawyer()
    {
        $data = file_get_contents("JsonExports/abogadoArea.json");
        $items = json_decode($data, true);
        $lawyersMapping = [];
        $activitiesMapping = [];
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $activityRepository = $this->em->getRepository(Activity::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_activity]");
        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_secondary_activity]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: lawyer:" . $item['id_abogado'] . " activity:" . $item['id_area']);
            $lawyerId = $this->getMappedLawyerId($item['id_abogado']);
            $activityId = $this->getMappedActivityId($item['id_area']);
            if ($lawyerId && $activityId) {

                // Trying to recover objects from the mapping arrays,
                // if items does not exists, use the ORM to load it from the database
                $lawyer = isset($lawyersMapping[$item['id_abogado']]) ? $lawyersMapping[$item['id_abogado']] : $lawyerRepository->find($lawyerId);
                $activity = isset($activitiesMapping[$item['id_area']]) ? $activitiesMapping[$item['id_area']] : $activityRepository->find($activityId);

                $this->logger->debug("- Mapped Lawyer " . $lawyer->getName());
                $this->logger->debug("- Mapped Activity " . $activity->translate("es")->getTitle());

                $isSecondary = ($item['principal'] == '0');
                if ($isSecondary) {
                    $lawyer->addSecondaryActivity($activity);
                } else {
                    $lawyer->addActivity($activity);
                }
                $this->em->persist($lawyer);
                $this->em->flush();

                // Adding lawyer and activity objects to the mapping arrays
                // in order to avoid ORM calls in each iteration
                $lawyersMapping[$item['id_abogado']] = $lawyer;
                $activitiesMapping[$item['id_area']] = $activity;
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function ActivitiesByEvent()
    {
        $data = file_get_contents("JsonExports/eventosArea.json");
        $items = json_decode($data, true);
        $eventRepository = $this->em->getRepository(Event::class);
        $activityRepository = $this->em->getRepository(Activity::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [event_activity]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: event:" . $item['id_evento'] . " activity:" . $item['id_area']);
            $eventId = $this->getMappedEventId($item['id_evento']);
            $activityId = $this->getMappedActivityId($item['id_area']);
            if ($eventId && $activityId) {
                $event = $eventRepository->find($eventId);
                $activity = $activityRepository->find($activityId);

                $this->logger->debug("- Mapped Event " . $event->translate("es")->getTitle());
                $this->logger->debug("- Mapped Activity " . $activity->translate("es")->getTitle());

                $event->addActivity($activity);
                $this->em->persist($event);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function PeopleByEvent()
    {
        $data = file_get_contents("JsonExports/eventosPonente.json");
        $items = json_decode($data, true);
        $processedPeople = [];

        $eventRepository = $this->em->getRepository(Event::class);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $personRepository = $this->em->getRepository(Person::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [event_person]");
        // $this->em->getConnection()->executeQuery("DELETE FROM [person]");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([person], RESEED, 1)");

        foreach ($items as $item) {

            // Skip Person other than lawyers
            if ($item['id_abogado']) {
                $this->logger->debug("ORIGINAL DATA: event:" . $item['id_evento'] . " lawyer:" . $item['id_abogado']);

                $eventId = $this->getMappedEventId($item['id_evento']);
                $lawyerId = $this->getMappedLawyerId($item['id_abogado']);

                if ($lawyerId && $eventId) {
                    $event = $eventRepository->find($eventId);
                    $lawyer = $lawyerRepository->find($lawyerId);

                    $this->logger->debug("- Mapped Event " . $eventId . " · " . $event->translate("es")->getTitle());
                    $this->logger->debug("- Mapped Lawyer " . $lawyerId . " · " . $lawyer->getFullName());

                    if (isset($processedPeople[$item['id_abogado']])) {
                        $person = $processedPeople[$item['id_abogado']];
                    } else {
                        $person = $personRepository->findOneBy(array('lawyer' => $lawyer));
                        if(!$person){
                            $person = new Person();
                            $person->setOldId($item['id']);
                            $person->setLawyer($lawyer);
                        }
                    }

                    $event->addPerson($person);

                    $this->em->persist($event);
                    $this->em->flush();

                    $processedPeople[$item['id_abogado']] = $person;
                } else {
                    $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
                }
            }
        }
    }
    public function Office()
    {
        $data = file_get_contents("JsonExports/oficinas.json");
        $items = json_decode($data, true);

        $data1 = file_get_contents("JsonExports/OficinaDescripcion.json");
        $items1 = json_decode($data1, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/office-*"));

        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE office_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [OfficeTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Office]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Office], RESEED, 1)");

        $processedOfficeMap = [];
        $processedAttachmentsMap = [];
        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            // if ($item['status']=='0' || $item['visible']=='0' || empty($item['titulo'])) {
            //     continue;
            // }

            $oldOfficeId = $item['id'];
            // Was processed the current event instance in a previous iteration ?
            if (isset($processedOfficeMap[$oldOfficeId])) {
                // in that case, restore it from $processedEventsMap

                $office = $processedOfficeMap[$oldOfficeId];
            } else {

                // in other case, create a new instance and fill it
                $office = new Office();
                $office->setOldId($oldOfficeId);
                $office->setCity($item['ciudad']);
                $office->setAddress($item['direccion']);
                $office->setCp($item['cp']);
                $office->setCountry($item['pais']);
                $office->setContact($item['contacto']);
                $office->setEmail($item['email']);
                $office->setFax($item['fax']);
                $office->setPhone($item['tel']);
                $office->setImgMap($item['img_map'] ? $item['img_map'] : '');
                $office->setLinkGoogle($item['link_google']);
                $office->setStatus($item['status']);
                $office->setPlace($item['lugar']);
                $office->setGeographicalArea($item['zonageografica'] ? $item['zonageografica'] : 0);
                $office->setSap($item['sap'] ? $item['sap'] : '');
            }
            foreach ($items1 as $item1) {
                if ($item1['id_oficina'] == $oldOfficeId) {
                    $currentLang = self::getMappedLanguageCode($item1['lang']);
                    if ($item1['lang']=="esp" || $item1['lang']=="eng" || $item1['lang']=="por" || $item1['lang']=="chi") {
                        if ($item['visio_'.$item1['lang']] == "1") {
                            $office->setLanguages(
                                array_unique(
                                    array_merge($office
                                ->getLanguages(), [$currentLang])
                                )
                            );
                        }
                    }

                    $office->translate($currentLang)->setDescriptions($item1['descripcion']);
                    $office->translate($currentLang)->setTags($item1['tags']);
                    $office->translate($currentLang)->setCity($item1['ciudad']);
                    $office->translate($currentLang)->setCountry($item1['pais'] ? $item1['pais'] : '');
                }
            }

            // Importing attachments from source
            if ($item['img_office']) {

                // Was processed the current attachment instance in a previous iteration ?
                if (isset($processedAttachmentsMap[$oldOfficeId][$item['img_office']])) {
                    $resource = $processedAttachmentsMap[$oldOfficeId][$item['img_office']];
                    $resource->setLanguages(
                        array_unique(
                            array_merge($resource->getLanguages(), [$currentLang])
                        )
                    );
                } else {
                    // normalizing the attachment path
                    $path = $item['img_office'];
                    $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                    $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                    $path = self::SOURCE_DOMAIN.$path;

                    $attachment = $this->importFile('office', $path);
                    if ($attachment) {
                        $resource = new Resource();
                        $resource->setFile($attachment);
                        $resource->setFileName($attachment->getFileName());
                        $resource->setLanguages([$currentLang]);
                        $office->setImgOffice($resource);
                        // Adding the current attachment to the attachments mapping
                        $processedAttachmentsMap[$oldOfficeId][$item['img_office']] = $resource;
                    }
                }
            }
            ///////////////////MIRARRRRRR/////////////////////////////

            // Adding the current instance to the offices mapping
            $processedOfficeMap[$oldOfficeId] = $office;
        }
        foreach ($processedOfficeMap as $office) {
            // Persist only the registers with at least one active language
            $office->mergeNewTranslations();
            $this->em->persist($office);
            $this->em->flush();
            $this->logger->debug("Office ".$office->getId());
        }
    }

    public function OfficeByLawyer()
    {
        $data = file_get_contents("JsonExports/OficinaAbogado.json");
        $items = json_decode($data, true);
        $lawerRepository = $this->em->getRepository(Lawyer::class);
        $officeRepository = $this->em->getRepository(Office::class);

        foreach ($items as $item) {
            // $this->logger->debug("ORIGINAL DATA: event:" . $item['id_evento'] . " activity:" . $item['id_area']);
            $lawyerId = $this->getMappedLawyerId($item['id_abogado']);
            $officeId = $this->getMappedOfficeId($item['id_oficina']);
            if ($lawyerId && $officeId) {
                $lawyer = $lawerRepository->find($lawyerId);
                $office = $officeRepository->find($officeId);
                $lawyer->setOffice($office);
                $this->em->persist($lawyer);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function OfficeByEvents()
    {
        $data = file_get_contents("JsonExports/OficinaEventos.json");
        $items = json_decode($data, true);
        $eventRepository = $this->em->getRepository(Event::class);
        $officeRepository = $this->em->getRepository(Office::class);

        foreach ($items as $item) {
            // $this->logger->debug("ORIGINAL DATA: event:" . $item['id_evento'] . " activity:" . $item['id_area']);
            $eventId = $this->getMappedEventId($item['id_evento']);
            $officeId = $this->getMappedOfficeId($item['id_oficina']);
            if ($eventId && $officeId) {
                $event = $eventRepository->find($eventId);
                $office = $officeRepository->find($officeId);
                $event->setOffice($office);
                $this->em->persist($event);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function awards()
    {
        $data = file_get_contents("JsonExports/premios.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/award-*"));

        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE award_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [AwardTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Award]");

        $processedAwardsMap = [];
        $processedAttachmentsMap = [];

        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if (!empty($item['title'])) {
                $oldAwardId = $item['id'];
                $currentLang = self::getMappedLanguageCode($item['lang']);

                // Was processed the current event instance in a previous iteration ?
                if (isset($processedAwardsMap[$oldAwardId])) {
                    // in that case, restore it from $processedEventsMap
                    $Award = $processedAwardsMap[$oldAwardId];
                } else {
                    // in other case, create a new instance and fill it
                    $Award = new Award();
                    $Award->setOldId($oldAwardId);
                    $Date = \DateTime::createFromFormat('Y-m-d G:i:s.u', $item['fecha']);
                    $Award->setDate(
                        $Date ? $Date : date("Y-m-d H:i:s")
                    );
                    $Award->setStatus($item['status']);
                }
                // Updating the languages field using the correspondent visio_* field
                if ($item['visio_'.$item['lang']] == "1") {
                    $Award->setLanguages(
                        array_unique(
                            array_merge($Award
                        ->getLanguages(), [$currentLang])
                        )
                    );
                }
                // Importing attachments from source
                if ($item['url_image']) {
                    // Was processed the current attachment instance in a previous iteration ?
                    if (isset($processedAttachmentsMap[$oldAwardId][$item['url_image']])) {
                        $resource = $processedAttachmentsMap[$oldAwardId][$item['url_image']];
                        $resource->setLanguages(
                            array_unique(
                                array_merge($resource->getLanguages(), [$currentLang])
                            )
                        );
                    } else {
                        // normalizing the attachment path
                        $path = $item['url_image'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.$path;

                        $attachment = $this->importFile('award', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages([$currentLang]);
                            $Award->setImage($resource);
                            // Adding the current attachment to the attachments mapping
                            $processedAttachmentsMap[$oldAwardId][$item['url_image']] = $resource;
                        }
                    }
                }
                // Filling translatable fields
                $Award->translate($currentLang)->setTitle($item['title']);
                $Award->translate($currentLang)->setGranted($item['otorgado']);
                $Award->translate($currentLang)->setDescAward($item['desc_award']);
                $Award->translate($currentLang)->setDescAwardFirma($item['desc_award_firma']);
                $Award->translate($currentLang)->setDescAwardIndiv($item['desc_award_indiv']);
                $Award->translate($currentLang)->setTags($item['tags']);
                // Adding the current instance to the events mapping
                $processedAwardsMap[$oldAwardId] = $Award;
            }
        }

        foreach ($processedAwardsMap as $Award) {
            // Persist only the registers with at least one active language
            if (!empty($Award->getLanguages())) {
                // Persist the instance
                $Award->mergeNewTranslations();
                $this->em->persist($Award);
                $this->em->flush();
                $this->logger->debug("Award ".$Award->getId()." ".$Award->translate('es')->getTitle());
            }
        }
    }

    public function Publications()
    {
        $publications = json_decode(
            file_get_contents("JsonExports/Publicaciones.json"),
            true
        );

        $publication_translations = json_decode(
            file_get_contents("JsonExports/PublicacionesIdiomas.json"),
            true
        );

        // Ordering the items using the publicacion_id column
        // in order to free memory while doing the loop
        usort($publication_translations, function ($a, $b) {
            if ($a['publicacion_id'] == $b['publicacion_id']) {
                return 0;
            }
            return ($a['publicacion_id'] < $b['publicacion_id']) ? -1 : 1;
        });

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/publication-*"));

        // $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE article_id IS NOT NULL");
        // $this->em->getConnection()->executeQuery("DELETE FROM [ArticleTranslation]");
        // $this->em->getConnection()->executeQuery("DELETE FROM [Article]");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Article], RESEED, 1)");

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($publications as $key => $item) {
            $oldPublicationId = $item['id'];
            // create a new instance and fill it
            if ($item['tipo_publicacion']==2) {
                $publication = new Article();
            } elseif ($item['tipo_publicacion']==3) {
                $publication = new Research();
            } elseif ($item['tipo_publicacion']==1 || $item['tipo_publicacion']==4 || $item['tipo_publicacion']==5 || $item['tipo_publicacion']==6 || $item['tipo_publicacion']==7) {
                $publication = new LegalNovelty();
            } else {
                continue;
            }
            if($publication){
                $publication->setOldId($oldPublicationId);
                $publication->setFeatured($item['destacada']);
                $publication->setPublicationDate(new \DateTime($item['fecha_publicacion']));
                if ($item['url_imagen']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['url_imagen']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']] = $resource;
                    } else {
                        $path = $item['url_imagen'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.'/media_repository/gabinete/'.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages(['es','en','pt','zh']);
                            $resource->setType('publication_main_photo');
                            $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']] = $resource;
                        }
                    }
                }
                if ($item['thumbnail']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['thumbnail']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']] = $resource;
                    } else {
                        $path = $item['thumbnail'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.'/media_repository/OutputTumbs/'.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages(['es','en','pt','zh']);
                            $resource->setType('publication_thumbnail');
                            $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']] = $resource;
                        }
                    }
                }

                $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                $this->logger->debug("New Publication : From $oldPublicationId ~> To ".$publication->getId());

                // Adding the current instance to the offices mapping
                $processedPublicationMap[$oldPublicationId] = $publication;
            }
        }

        // Resetting the attachment's collection to improve the  process performance
        unset($processedAttachmentsMap);
        // To force garbage collector to do its job
        gc_collect_cycles();

        $this->logger->debug("Actualizando traducciones de artículos...");

        // Just to control when the publicacion_id changes during the loop
        $lastId = 0;

        // Attaching translations
        foreach ($publication_translations as $index => $item1) {
            $oldPublicationId = $item1['publicacion_id'];

            // The order of the items in the collection guarantees us that
            // there is no more registers with id = lastId in the collection
            // Then, we can persist the article with id = lastId
            // and remove it to free memory
            if ($lastId != 0 && $lastId != $oldPublicationId) {
                $publication = $processedPublicationMap[$lastId] ?? null;
                if ($publication) {
                    if (!empty($publication->getLanguages())) {
                        $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                        $this->logger->debug("Updating Publication ".$publication->getId());
                        if(isset($processedPublicationMap[$lastId]))
                        unset($processedPublicationMap[$lastId]);
                        if(isset($processedAttachmentsMap[$lastId]))
                        unset($processedAttachmentsMap[$lastId]);
                        // To force garbage collector to do its job
                        gc_collect_cycles();
                    }
                }
            }

            $publication = $processedPublicationMap[$oldPublicationId] ?? null;

            if ($publication) {
                $currentLang = self::getMappedLanguageCode($item1['lang']);
                if ($currentLang && isset($item1['title']) && $item1['title'] != '') {
                    $publication->translate($currentLang)->setTitle(isset($item1['title']) ? $item1['title'] : 'Notitle');
                    $publication->translate($currentLang)->setSummary($item1['summary']  ? $item1['summary'] : '');
                    $publication->translate($currentLang)->setContent($item1['contenido']  ? $item1['contenido'] : '');
                    $publication->setLanguages(
                        array_unique(
                            array_merge(
                                $publication->getLanguages(),
                                [$currentLang]
                            )
                        )
                    );
                }
                if ($item1['url_pdf']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']];
                        $resource->setLanguages(
                            array_unique(
                                array_merge($resource->getLanguages(), [$currentLang])
                            )
                        );
                        $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']] = $resource;
                    } else {
                        $path = $item1['url_pdf'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages([$currentLang]);
                            $resource->setType('publication_dossier');
                            $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']] = $resource;
                        }
                    }
                }
                // Adding the current instance to the offices mapping
                $processedPublicationMap[$oldPublicationId] = $publication;

                // Is the last item in the collection
                if ($index+1 == count($publication_translations)) {
                    if (!empty($publication->getLanguages())) {
                        $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                        $this->logger->debug("Updating Publication ".$publication->getId());
                    }
                }
            }

            $lastId = $oldPublicationId;
        }
    }
    

    protected function persistPublication($publication, $attachments=[])
    {
        $publication->mergeNewTranslations();
        // Persist only the registers with at least one active language
        foreach ($attachments as $key => $resource) {
            $publication->addAttachment($resource);
        }
        $this->em->persist($publication);
        $this->em->flush();
    }

    public function PublicationsByLawyers()
    {
        $data = file_get_contents("JsonExports/PublicacionesAbogados.json");
        $items = json_decode($data, true);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $publicationRepository = $this->em->getRepository(Publication::class);
        $personRepository = $this->em->getRepository(Person::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [publication_person]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " Lawyer:" . $item['abogado_id']);
            $publicationId = $this->getMappedPublicationId($item['publicacion_id']);
            $lawyerId = $this->getMappedLawyerId($item['abogado_id']);
            if ($publicationId && $lawyerId) {
                $publication = $publicationRepository->find($publicationId);
                $lawyer = $lawyerRepository->find($lawyerId);
                $person = $personRepository->findOneBy(array('lawyer' => $lawyer));
                if(!$person){
                    $person = new Person();
                    $person->setOldId($lawyerId);
                    $person->setLawyer($lawyer);
                }
                $publication->addPerson($person);
                $this->em->persist($publication);
                $this->em->flush();
                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped lawyer " . $lawyer->translate("es")->getTitle());
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function PublicationsByOffices()
    {
        $data = file_get_contents("JsonExports/PublicacionesOficina.json");
        $items = json_decode($data, true);
        $officeRepository = $this->em->getRepository(Office::class);
        $publicationRepository = $this->em->getRepository(Publication::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [publication_office]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " office:" . $item['oficina_id']);
            $publicationId = $this->getMappedPublicationId($item['publicacion_id']);
            $oficceId = $this->getMappedOfficeId($item['oficina_id']);
            if ($publicationId && $oficceId) {
                $publication = $publicationRepository->find($publicationId);
                $office = $officeRepository->find($oficceId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addOffice($office);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function PublicationsByActivities()
    {
        $data = file_get_contents("JsonExports/PublicacionesPractica.json");
        $items = json_decode($data, true);
        $activityRepository = $this->em->getRepository(Activity::class);
        $publicationRepository = $this->em->getRepository(Publication::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [publication_activity]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " practica:" . $item['practica_id']);
            $publicationId = $this->getMappedPublicationId($item['publicacion_id']);
            $practicaId = $this->getMappedActivityId($item['practica_id']);
            if ($publicationId && $practicaId) {
                $publication = $publicationRepository->find($publicationId);
                $practica = $activityRepository->find($practicaId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addActivity($practica);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function ArticlesCategory()
    {
        $client = HttpClient::create();
        $categorias = ["propiedad-intelectual","competencia","deporte-entretenimiento","mercado-de-valores","laboral",""];
        //Categorias en español
        foreach ($categorias as $key => $categoria) {
            for ($i = 1; $i <= 2; $i++) {
                $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoria.'/wp-json/wp/v2/categories?per_page=80&page='.$i);
                $status = $response->getStatusCode();
                if ($status!=400) {
                    $content = $response->toArray();
                    foreach ($content as $key2 => $value2) {
                        $ArticleCategory = new ArticleCategory();
                        $ArticleCategory->setOldId($value2["id"]);
                        $ArticleCategory->translate('es')->setTitle($value2["name"]);
                        $ArticleCategory->translate('es')->setOldlink($value2["link"]);
                        $ArticleCategory->setLanguages(
                            array_unique(
                                array_merge($ArticleCategory
                                    ->getLanguages(), ['es'])
                            )
                        );
                        $response1 =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoria.'/wp-json/wp/v2/categories?include='.$value2['id'].'&wpml_language=en');
                        $content1 = $response1->toArray();
                        if (isset($content1[0])) {
                            $ArticleCategory->translate('en')->setTitle($content1[0]["name"]);
                            $ArticleCategory->translate('en')->setOldlink($content1[0]["link"]);
                            $ArticleCategory->setLanguages(
                                array_unique(
                                    array_merge($ArticleCategory
                                        ->getLanguages(), ['en'])
                                )
                            );
                        }
                        $ArticleCategory->mergeNewTranslations();
                        $this->em->persist($ArticleCategory);
                        $this->em->flush();
                    }
                }
                if ($status=400) {
                    break;
                }
            }
        }
        //Categorias en ingles
        foreach ($categorias as $key => $categoria) {
            for ($i = 1; $i <= 2; $i++) {
                $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoria.'/wp-json/wp/v2/categories?per_page=80&lang=en&page='.$i);
                $status = $response->getStatusCode();
                if ($status!=400) {
                    $content = $response->toArray();
                    foreach ($content as $key2 => $value2) {
                        $ArticleCategory = new ArticleCategory();
                        $ArticleCategory->setOldId($value2["id"]);
                        $ArticleCategory->translate('en')->setTitle($value2["name"]);
                        $ArticleCategory->translate('en')->setOldlink($value2["link"]);
                        $ArticleCategory->setLanguages(
                            array_unique(
                                array_merge($ArticleCategory
                                    ->getLanguages(), ['en'])
                            )
                        );
                        $response1 =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoria.'/wp-json/wp/v2/categories?include='.$value2['id'].'&wpml_language=es');
                        $content1 = $response1->toArray();
                        if (!isset($content1[0])) {
                            $ArticleCategory->mergeNewTranslations();
                            $this->em->persist($ArticleCategory);
                            $this->em->flush();
                        }
                    }
                }
                if ($status=400) {
                    break;
                }
            }
        }
    }
    public function ArticlesAuthors()
    {
        $client = HttpClient::create();
        $categorias = ["propiedad-intelectual","competencia","deporte-entretenimiento","mercado-de-valores","laboral",""];
        //Categorias en español
        foreach ($categorias as $key => $categoria) {
                $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoria.'/wp-json/wp/v2/users?per_page=80');
                $status = $response->getStatusCode();
                if ($status!=400) {
                    $content = $response->toArray();
                    foreach ($content as $key2 => $value2) {
                        $person = new Person();
                        $person->setOldId($value2["id"]);
                        $fullName = explode(" ", $value2["name"],2);
                        $person->setName($fullName[0]);
                        if(count($fullName)>1){
                            $person->setSurname($fullName[1]);
                        }
                        $this->em->persist($person);
                        $this->em->flush();
                    }
                }
        }
    }
    public function ArticlesPost()
    {
        $ArticleCategoryRepository = $this->em->getRepository(ArticleCategory::class);
        $client = HttpClient::create();
        $categorias = $ArticleCategoryRepository->findAll();
        foreach ($categorias as $keyCategory => $categoria) {
            $Languages = $categoria->getLanguages();
            if ($Languages[0] == 'es') {
                $Oldlink = $categoria->translate('es')->getOldlink();
                $Oldlinka = explode('/', $Oldlink);
                $categoriaLink = $Oldlinka[3]!="categoria" ? $Oldlinka[3] : "";
                for ($i = 1; $i <= 20; $i++) {
                    $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoriaLink.'/wp-json/wp/v2/posts?categories='.$categoria->getOldId().'&page='.$i);
                    $status = $response->getStatusCode();
                    if ($status!=400) {
                        $content = $response->toArray();
                        foreach ($content as $keyPost => $post) {
                            $article = new Article();
                            $article->setOldId($post['id']);
                            $article->setStatus($post['status']=='publish' ? 1 : 0);
                            $article->setPublicationDate(new \DateTime($post['date']));
                            $article->translate('es')->setTitle($post['title'] ? $post['title']['rendered'] : '');
                            $article->translate('es')->setSummary($post['content']['rendered']);
                            $article->translate('es')->setContent($post['excerpt']['rendered']);
                            $article->setLanguages(
                                array_unique(
                                    array_merge($article
                                    ->getLanguages(), ['es'])
                                )
                            );
                            if($post['author']){
                                $personRepository = $this->em->getRepository(Person::class);
                                $personId = $this->getMappedPersonId($post['author']);
                                if($personId){
                                    $person = $personRepository->find($personId);
                                    $article->addPerson($person);
                                }
                                
                            }
                            foreach ($post['categories'] as $keyCategory => $postCategory) {
                                $articleCategoryId = $this->getMappedArticleCategoryId($postCategory);
                                if($articleCategoryId){
                                    $articleCategory = $ArticleCategoryRepository->find($articleCategoryId);
                                    $article->addCategory($articleCategory);
                                }
                            }
                            $responseEn =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoriaLink.'/wp-json/wp/v2/posts?include='.$post['id'].'&wpml_language=en');
                            $contentEn = $responseEn->toArray();
                            if (isset($contentEn[0])) {
                                $article->translate('en')->setTitle($contentEn[0]['title']['rendered']);
                                $article->translate('en')->setSummary($contentEn[0]['content']['rendered']);
                                $article->translate('en')->setContent($contentEn[0]['excerpt']['rendered']);
                                $article->setLanguages(
                                    array_unique(
                                        array_merge($article
                                        ->getLanguages(), ['en'])
                                    )
                                );
                            }
                            $article->mergeNewTranslations();
                            $this->em->persist($article);
                            $this->em->flush();
                        }
                    }
                    if ($status=400) {
                        break;
                    }
                }
            } else {
                $Oldlink = $categoria->translate('en')->getOldlink();
                $Oldlinka = explode('/', $Oldlink);
                $categoriaLink = $Oldlinka[3]!="categoria" ? $Oldlinka[3] : "";
                for ($i = 1; $i <= 20; $i++) {
                    $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoriaLink.'/wp-json/wp/v2/posts?lang=en&categories='.$categoria->getOldId().'&page='.$i);
                    $status = $response->getStatusCode();
                    if ($status!=400) {
                        $content = $response->toArray();
                        foreach ($content as $keyPost => $post) {
                            $article = new Article();
                            $article->setOldId($post['id']);
                            $article->setStatus($post['status']=='publish' ? 1 : 0);
                            if($post['author']){
                                $personRepository = $this->em->getRepository(Person::class);
                                $personId = $this->getMappedPersonId($post['author']);
                                if($personId){
                                    $person = $personRepository->find($personId);
                                    $article->addPerson($person);
                                }
                            }
                            
                            $article->translate('en')->setTitle($post['title']['rendered']);
                            $article->translate('en')->setSummary($post['content']['rendered']);
                            $article->translate('en')->setContent($post['excerpt']['rendered']);
                            $article->setLanguages(
                                array_unique(
                                    array_merge($article
                                    ->getLanguages(), ['en'])
                                )
                            );
                            foreach ($post['categories'] as $keyCategory => $postCategory) {
                                $articleCategoryId = $this->getMappedArticleCategoryId($postCategory);
                                if($articleCategoryId){
                                    $articleCategory = $ArticleCategoryRepository->find($articleCategoryId);
                                    $article->addCategory($articleCategory);
                                }
                            }
                            $responseEn =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoriaLink.'/wp-json/wp/v2/posts?include='.$post['id'].'&wpml_language=es');
                            $contentEn = $responseEn->toArray();
                            if (isset($contentEn[0])) {
                                $article->translate('es')->setTitle($contentEn[0]['title']['rendered']);
                                $article->translate('es')->setSummary($contentEn[0]['content']['rendered']);
                                $article->translate('es')->setContent($contentEn[0]['excerpt']['rendered']);
                                $article->setLanguages(
                                    array_unique(
                                        array_merge($article
                                        ->getLanguages(), ['es'])
                                    )
                                );
                            }
                            $article->mergeNewTranslations();
                            $this->em->persist($article);
                            $this->em->flush();
                        }
                    }
                    if ($status=400) {
                        break;
                    }
                }
            }
        }
    }
    public function ArticlesPostFiles()
    {
        $client = HttpClient::create();
        $ArticleRepository = $this->em->getRepository(Article::class);
        $articulos = $ArticleRepository->findAll();
        foreach ($articulos as $keyArticulo => $articulo) {
            $categorias = $articulo->getCategory();
            foreach ($categorias as $keycategoria => $categoria) {
                $LanguagesCategoria = $categoria->getLanguages();
                $Oldlink = $categoria->translate($LanguagesCategoria[0])->getOldlink();
                $Oldlinka = explode('/', $Oldlink);
                $categoriaLink = $Oldlinka[3]!="categoria" ? $Oldlinka[3] : "";
                $response =  $client->request('GET', 'https://blog.cuatrecasas.com/'.$categoriaLink.'/wp-json/wp/v2/media?parent='.$articulo->getOldId());
                $status = $response->getStatusCode();
                if ($status!=400) {
                    $content = $response->toArray();
                    if (isset($content[0])) {
                        $photo = $this->importFile('article', $content[0]['guid']['rendered']);
                        if ($photo) {
                            $resource = new Resource();
                            $resource->setFile($photo);
                            $resource->setFileName($photo->getFileName());
                            $resource->setArticle($articulo);
                            $resource->setType('article_main_photo');
                            $articulo->addAttachment($resource);
                            $this->em->persist($articulo);
                            $this->em->flush();
                        }
                    }
                }
            }
        }
    }

    public function News()
    {
        $publications = json_decode(
            file_get_contents("JsonExports/noticias.json"),
            true
        );

        $publication_translations = json_decode(
            file_get_contents("JsonExports/noticiasIdioma.json"),
            true
        );

        // Ordering the items using the publicacion_id column
        // in order to free memory while doing the loop
        usort($publication_translations, function ($a, $b) {
            if ($a['noticias_id'] == $b['noticias_id']) {
                return 0;
            }
            return ($a['noticias_id'] < $b['noticias_id']) ? -1 : 1;
        });

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/news-*"));

        // $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE article_id IS NOT NULL");
        // $this->em->getConnection()->executeQuery("DELETE FROM [ArticleTranslation]");
        // $this->em->getConnection()->executeQuery("DELETE FROM [Article]");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Article], RESEED, 1)");

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($publications as $key => $item) {
            $oldPublicationId = $item['id'];
            // create a new instance and fill it
            if($item['tipo_noticia']==3){
                $publication = new News();
            }
            if($item['tipo_noticia']==4){
                $publication = new News();
            }
            if($item['tipo_noticia']==5){
                $publication = new Opinion();
            }
            if(isset($publication)){
                $publication->setOldId($oldPublicationId);
                $publication->setFeatured($item['destacada'] ? $item['destacada'] : 0);
                $publication->setPublicationDate(new \DateTime($item['fecha_publicacion']));
				$currentLang = self::getMappedLanguageCode($item['lang']);
                if ($currentLang && isset($item['title']) && $item['title'] != '') {
					$publication->translate($currentLang)->setTitle(isset($item['title']) ? $item['title'] : 'Notitle');
                    $publication->translate($currentLang)->setSummary($item['summary']  ? $item['summary'] : '');
                    $publication->translate($currentLang)->setContent($item['contenido']  ? $item['contenido'] : '');
                    $publication->setLanguages(
                        array_unique(
                            array_merge(
                                $publication->getLanguages(),
                                [$currentLang]
                            )
                        )
                    );
				}
				
                if ($item['url_imagen']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['url_imagen']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']] = $resource;
                    } else {
                        $path = $item['url_imagen'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.'/media_repository/gabinete/'.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages(['es','en','pt','zh']);
                            $resource->setType('publication_main_photo');
                            $processedAttachmentsMap[$oldPublicationId][$item['url_imagen']] = $resource;
                        }
                    }
                }
                if ($item['thumbnail']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['thumbnail']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']] = $resource;
                    } else {
                        $path = $item['thumbnail'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.'/media_repository/OutputTumbs/'.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages(['es','en','pt','zh']);
                            $resource->setType('publication_thumbnail');
                            $processedAttachmentsMap[$oldPublicationId][$item['thumbnail']] = $resource;
                        }
                    }
                }
				if ($item['url_pdf']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['url_pdf']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['url_pdf']];
                        $resource->setLanguages(
                            array_unique(
                                array_merge($resource->getLanguages(), [$currentLang])
                            )
                        );
                        $processedAttachmentsMap[$oldPublicationId][$item['url_pdf']] = $resource;
                    } else {
                        $path = $item['url_pdf'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages([$currentLang]);
                            $resource->setType('publication_dossier');
                            $processedAttachmentsMap[$oldPublicationId][$item['url_pdf']] = $resource;
                        }
                    }
                }

                $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                $this->logger->debug("New Publication : From $oldPublicationId ~> To ".$publication->getId());

                // Adding the current instance to the offices mapping
                $processedPublicationMap[$oldPublicationId] = $publication;
            }
        }

        // Resetting the attachment's collection to improve the  process performance
        unset($processedAttachmentsMap);
        // To force garbage collector to do its job
        gc_collect_cycles();

        $this->logger->debug("Actualizando traducciones de artículos...");

        // Just to control when the noticias_id changes during the loop
        $lastId = 0;

        // Attaching translations
        foreach ($publication_translations as $index => $item1) {
            $oldPublicationId = $item1['noticias_id'];

            // The order of the items in the collection guarantees us that
            // there is no more registers with id = lastId in the collection
            // Then, we can persist the article with id = lastId
            // and remove it to free memory
            if ($lastId != 0 && $lastId != $oldPublicationId) {
                $publication = $processedPublicationMap[$lastId] ?? null;
                if ($publication) {
                    if (!empty($publication->getLanguages())) {
                        $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                        $this->logger->debug("Updating Publication ".$publication->getId());
                        if($processedPublicationMap[$lastId])
                        unset($processedPublicationMap[$lastId]);
                        if($processedAttachmentsMap[$lastId])
                        unset($processedAttachmentsMap[$lastId]);
                        // To force garbage collector to do its job
                        gc_collect_cycles();
                    }
                }
            }

            $publication = $processedPublicationMap[$oldPublicationId] ?? null;

            if ($publication) {
                $currentLang = self::getMappedLanguageCodeById($item1['idiomas_id']);
                if ($currentLang && isset($item1['title']) && $item1['title'] != '') {
                    $publication->translate($currentLang)->setTitle(isset($item1['title']) ? $item1['title'] : 'Notitle');
                    $publication->translate($currentLang)->setSummary($item1['summary']  ? $item1['summary'] : '');
                    $publication->translate($currentLang)->setContent($item1['contenido']  ? $item1['contenido'] : '');
                    $publication->setLanguages(
                        array_unique(
                            array_merge(
                                $publication->getLanguages(),
                                [$currentLang]
                            )
                        )
                    );
                }
                if ($item1['url_pdf']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']];
                        $resource->setLanguages(
                            array_unique(
                                array_merge($resource->getLanguages(), [$currentLang])
                            )
                        );
                        $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']] = $resource;
                    } else {
                        $path = $item1['url_pdf'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages([$currentLang]);
                            $resource->setType('publication_dossier');
                            $processedAttachmentsMap[$oldPublicationId][$item1['url_pdf']] = $resource;
                        }
                    }
                }
                // Adding the current instance to the offices mapping
                $processedPublicationMap[$oldPublicationId] = $publication;

                // Is the last item in the collection
                if ($index+1 == count($publication_translations)) {
                    if (!empty($publication->getLanguages())) {
                        $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                        $this->logger->debug("Updating Publication ".$publication->getId());
                    }
                }
            }

            $lastId = $oldPublicationId;
        }
    }
    public function NewsByLawyers()
    {
        $data = file_get_contents("JsonExports/NoticiasAbogados.json");
        $items = json_decode($data, true);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $publicationRepository = $this->em->getRepository(Publication::class);
        $personRepository = $this->em->getRepository(Person::class);



        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['noticia_id'] . " Lawyer:" . $item['abogado_id']);
            $publicationId = $this->getMappedPublicationId($item['noticia_id']);
            $lawyerId = $this->getMappedLawyerId($item['abogado_id']);
            if ($publicationId && $lawyerId) {
                $publication = $publicationRepository->find($publicationId);
                $lawyer = $lawyerRepository->find($lawyerId);
                $person = $personRepository->findOneBy(array('lawyer' => $lawyer));
                if(!$person){
                    $person = new Person();
                    $person->setOldId($lawyerId);
                    $person->setLawyer($lawyer);
                }
                $publication->addPerson($person);
                $this->em->persist($publication);
                $this->em->flush();
                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped lawyer " . $lawyer->translate("es")->getTitle());
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function NewsByOffices()
    {
        $data = file_get_contents("JsonExports/NoticiaOficina.json");
        $items = json_decode($data, true);
        $officeRepository = $this->em->getRepository(Office::class);
        $publicationRepository = $this->em->getRepository(Publication::class);


        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['noticia_id'] . " office:" . $item['oficina_id']);
            $publicationId = $this->getMappedPublicationId($item['noticia_id']);
            $oficceId = $this->getMappedOfficeId($item['oficina_id']);
            if ($publicationId && $oficceId) {
                $publication = $publicationRepository->find($publicationId);
                $office = $officeRepository->find($oficceId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addOffice($office);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function NewsByActivities()
    {
        $data = file_get_contents("JsonExports/NoticiaPractica.json");
        $items = json_decode($data, true);
        $activityRepository = $this->em->getRepository(Activity::class);
        $publicationRepository = $this->em->getRepository(Publication::class);


        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Noticia:" . $item['noticia_id'] . " practica:" . $item['practica_id']);
            $publicationId = $this->getMappedPublicationId($item['noticia_id']);
            $practicaId = $this->getMappedActivityId($item['practica_id']);
            if ($publicationId && $practicaId) {
                $publication = $publicationRepository->find($publicationId);
                $practica = $activityRepository->find($practicaId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addActivity($practica);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function regenerateSlugPublication()
    {
        $PublicationRepository = $this->em->getRepository(Publication::class);
        $publications = $PublicationRepository->findAll();
        foreach ($publications as $keyPublications => $publication) {
            $Languages = $publication->getLanguages();
            if($Languages){
                foreach ($Languages as $key => $Language) {
                    $publication->translate($Language)->setSlug( $publication->translate($Language)->getTitle());

                }
                $this->em->persist($publication);
                $this->em->flush();
            }
        }
    }
    private function getMappedEventId(?string $id): ?string
    {
        if (empty($this->mappedEventIds)) {
            $this->loadMappedEventIds();
        }
        return isset($this->mappedEventIds[$id]) ? $this->mappedEventIds[$id] : null;
    }

    private function loadMappedEventIds()
    {
        $repository = $this->em->getRepository(Event::class);
        $events = $repository->findAll();
        foreach ($events as $event) {
            $this->mappedEventIds[$event->getOldId()] = $event->getId();
        }
    }

    private function getMappedActivityId(?string $id): ?string
    {
        if (empty($this->mappedActivityIds)) {
            $this->loadMappedActivityIds();
        }
        return isset($this->mappedActivityIds[$id]) ? $this->mappedActivityIds[$id] : null;
    }

    private function loadMappedActivityIds()
    {
        $repository = $this->em->getRepository(Activity::class);
        $activities = $repository->findAll();
        foreach ($activities as $activity) {
            $this->mappedActivityIds[$activity->getOldId()] = $activity->getId();
        }
    }

    private function getMappedLawyerId(?string $id): ?string
    {
        if (empty($this->mappedLawyerIds)) {
            $this->loadMappedLawyerIds();
        }
        return isset($this->mappedLawyerIds[$id]) ? $this->mappedLawyerIds[$id] : null;
    }

    private function loadMappedLawyerIds()
    {
        $repository = $this->em->getRepository(Lawyer::class);
        $lawyers = $repository->findAll();
        foreach ($lawyers as $lawyer) {
            $this->mappedLawyerIds[$lawyer->getOldId()] = $lawyer->getId();
        }
    }

    private function getMappedOfficeId(?string $id): ?string
    {
        if (empty($this->mappedOfficeIds)) {
            $this->loadMappedOfficeIds();
        }
        return isset($this->mappedOfficeIds[$id]) ? $this->mappedOfficeIds[$id] : null;
    }

    private function loadMappedOfficeIds()
    {
        $repository = $this->em->getRepository(Office::class);
        $Offices = $repository->findAll();
        foreach ($Offices as $Office) {
            $this->mappedOfficeIds[$Office
            ->getOldId()] = $Office->getId();
        }
    }

    private function getMappedPublicationId(?string $id): ?string
    {
        if (empty($this->mappedPublicationIds)) {
            $this->loadMappedPublicationIds();
        }
        return isset($this->mappedPublicationIds[$id]) ? $this->mappedPublicationIds[$id] : null;
    }

    private function loadMappedPublicationIds()
    {
        $repository = $this->em->getRepository(Publication::class);
        $publications = $repository->findAll();
        foreach ($publications as $publication) {
            $this->mappedPublicationIds[$publication
            ->getOldId()] = $publication->getId();
        }
    }

    private function getMappedArticleCategoryId(?string $id): ?string
    {
        if (empty($this->mappedArticleCategoryIds)) {
            $this->loadMappedArticleCategoryIds();
        }
        return isset($this->mappedArticleCategoryIds[$id]) ? $this->mappedArticleCategoryIds[$id] : null;
    }

    private function loadMappedArticleCategoryIds()
    {
        $repository = $this->em->getRepository(ArticleCategory::class);
        $ArticleCategory = $repository->findAll();
        foreach ($ArticleCategory as $Category) {
            $this->mappedArticleCategoryIds[$Category
            ->getOldId()] = $Category->getId();
        }
    }
    private function getMappedPersonId(?string $id): ?string
    {
        if (empty($this->mappedPersonIds)) {
            $this->loadMappedPersonIds();
        }
        return isset($this->mappedPersonIds[$id]) ? $this->mappedPersonIds[$id] : null;
    }

    private function loadMappedPersonIds()
    {
        $repository = $this->em->getRepository(Person::class);
        $People = $repository->findAll();
        foreach ($People as $person) {
            $this->mappedPersonIds[$person
            ->getOldId()] = $person->getId();
        }
    }
    private static function getMappedLawyerType(?string $code): ?string
    {
        $map = [
            '0' => 'no_position',
            '1' => 'senior_associate',
            '2' => 'partner',
            '3' => 'counsel',
            '4' => 'senior_associate',
            '5' => 'partner',
            '6' => 'counsel',
            '7' => 'associate',
            '8' => 'honorary_partner',
            '9' => 'associate',
            '10' => 'honorary_partner',
            '11' => 'senior_partner',
            '12' => 'senior_partner',
            '13' => 'managing_partner',
            '14' => 'managing_partner',
            '15' => 'honorary_president',
        ];
        return isset($map[$code]) ? $map[$code] : null;
    }

    private static function getMappedLanguageCode(?string $code): ?string
    {
        $map = [
            'esp' => 'es',
            'eng' => 'en',
            'por' => 'pt',
            'chi' => 'zh'
        ];
        return isset($map[$code]) ? $map[$code] : null;
    }
    private static function getMappedLanguageCodeById(?string $code): ?string
    {
        $map = [
            '1' => 'es',
            '2' => 'en',
            '3' => 'pt',
            '4' => 'zh'
        ];
        return isset($map[$code]) ? $map[$code] : null;
    }



    private static function getMappedEventTypeCode(?string $code): ?string
    {
        $map = [
            "1" => "standard",
            "2" => "webinar",
            "3" => "breakfast",
            "4" => "institutional"
        ];
        return isset($map[$code]) ? $map[$code] : null;
    }

    private function importFile($type, $source)
    {
        $target_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        $target_filename = $type . "-" . \uniqid() . (strrchr($source, '.') ?? '');
        $target = $target_path . "/" . $target_filename;
        try {
            copy($source, $target);
            $file = new File($target);
        } catch (\Exception $e) {
            $this->logger->warning(">>>>>>>>>>>>>>>> ERROR COPYING $source into $target");
            $file = null;
        }
        return $file;
    }
}

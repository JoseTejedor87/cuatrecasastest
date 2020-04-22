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

use App\Entity\Activity;
use App\Entity\Awards;
use App\Entity\Desk;
use App\Entity\Event;
use App\Entity\Lawyer;
use App\Entity\Mention;
use App\Entity\Practice;
use App\Entity\Quote;
use App\Entity\Resource;
use App\Entity\Sector;
use App\Entity\Speaker;
use App\Entity\Office;
use App\Entity\Articles;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private $container;
    private $logger;
    private $mappedLawyerIds;
    private $mappedEventIds;
    private $mappedActivityIds;
    private $mappedOfficeIds;

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
            $this->SpeakersByEvent();
            $this->ActivitiesByEvent();
            $this->ActivitiesByLawyer();
            $this->Quote();
            $this->Office();
            $this->OfficeByLawyer();
            $this->awards();
            $this->Articles();
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
                case "speaker":
                    $this->SpeakersByEvent();
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
                case "awards":
                    $this->awards();
                    break;
                case "articles":
                    $this->Articles();
                    break;
                case "articlesByLawyers":
                    $this->ArticlesByLawyers();
                    break;
                case "articlesByOffices":
                    $this->ArticlesByOffices();
                    break;
                case "articlesByActivities":
                    $this->ArticlesByActivities();
                    break;
                case "ArticlesupdatePublicationDate":
                    $this->ArticlesupdatePublicationDate();
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
        $this->em->getConnection()->executeQuery("DELETE FROM [Office] ");
        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE lawyer_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [Speaker]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Speaker], RESEED, 1)");
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
            $lawyer->translate($currentLang)->setTraining($item['formacion']);
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
                //$event->setPhone($item['telefono']);
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
            $description = $item['descripcion'] . "<br/><br/>" . $item['experiencia'];
            $activity->translate($currentLang)->setDescription($description);

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

    public function SpeakersByEvent()
    {
        $data = file_get_contents("JsonExports/eventosPonente.json");
        $items = json_decode($data, true);
        $processedSpeakers = [];

        $eventRepository = $this->em->getRepository(Event::class);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [event_speaker]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Speaker]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Speaker], RESEED, 1)");

        foreach ($items as $item) {

            // Skip speakers other than lawyers
            if ($item['id_abogado']) {
                $this->logger->debug("ORIGINAL DATA: event:" . $item['id_evento'] . " lawyer:" . $item['id_abogado']);

                $eventId = $this->getMappedEventId($item['id_evento']);
                $lawyerId = $this->getMappedLawyerId($item['id_abogado']);

                if ($lawyerId && $eventId) {
                    $event = $eventRepository->find($eventId);
                    $lawyer = $lawyerRepository->find($lawyerId);

                    $this->logger->debug("- Mapped Event " . $eventId . " · " . $event->translate("es")->getTitle());
                    $this->logger->debug("- Mapped Lawyer " . $lawyerId . " · " . $lawyer->getFullName());

                    if (isset($processedSpeakers[$item['id_abogado']])) {
                        $speaker = $processedSpeakers[$item['id_abogado']];
                    } else {
                        $speaker = new Speaker();
                        $speaker->setOldId($item['id']);
                        $speaker->setLawyer($lawyer);
                    }

                    $event->addSpeaker($speaker);

                    $this->em->persist($event);
                    $this->em->flush();

                    $processedSpeakers[$item['id_abogado']] = $speaker;
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

        //$this->em->getConnection()->executeQuery("DELETE FROM [event_activity]");

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

    public function awards()
    {
        $data = file_get_contents("JsonExports/premios.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/award-*"));

        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE award_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [AwardsTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Awards]");

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
                    $Award = new Awards();
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
                            $Award->setImgOffice($resource);
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

    public function Articles()
    {
        $data = file_get_contents("JsonExports/Publicaciones.json");
        $items = json_decode($data, true);

        $data1 = file_get_contents("JsonExports/PublicacionesIdiomas.json");
        $items1 = json_decode($data1, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/article-*"));

        
        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE article_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [ArticlesTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Articles]");
        $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Articles], RESEED, 1)");

        $processedArticleMap = [];
        $processedAttachmentsMap = [];
        foreach ($items as $key => $item) {
            // if($key<=10){
            // $item = $items[0];
            $oldArticleId = $item['id'];
            // Was processed the current event instance in a previous iteration ?
            if (isset($processedArticleMap[$oldArticleId])) {
                // in that case, restore it from $processedEventsMap
                $article = $processedArticleMap[$oldArticleId];
            } else {
                
                // in other case, create a new instance and fill it
                $article = new Articles();
                $article->setOldId($oldArticleId);
                $article->setStatus($item['status']);
                $article->setFeatured($item['destacada']);
                $article->setPublicationDate(new \DateTime($item['fecha_publicacion']));
                
            }
            
            foreach ($items1 as $item1) {
                if($item1['publicacion_id'] == $oldArticleId){
                    $currentLang = self::getMappedLanguageCode($item1['lang']);
                    if(isset($item1['title']) && $item1['title'] != ''){
                        $article->translate($currentLang)->setTitle(isset($item1['title']) ? $item1['title'] : 'Notitle');
                        $article->translate($currentLang)->setSummary($item1['summary']  ? $item1['summary'] : '');
                        $article->translate($currentLang)->setContent($item1['contenido']  ? $item1['contenido'] : '');
                        $article->translate($currentLang)->setCaption($item1['pie_foto'] ? $item1['pie_foto'] : '');
                        $article->translate($currentLang)->setUrlLink($item1['url_link'] ? $item1['url_link'] : '');
                        $article->translate($currentLang)->setTags($item1['tags'] ? $item1['tags'] : '');
                        $article->translate($currentLang)->setLawyerTags($item1['abogado_tags'] ? $item1['abogado_tags'] :'');
                        $article->translate($currentLang)->setOfficeTags($item1['oficina_tags'] ? $item1['oficina_tags'] : '');
                        $article->translate($currentLang)->setPracticeTags($item1['practica_tags'] ? $item1['practica_tags'] :'');
                    
                        if($item1['lang']=="esp" || $item1['lang']=="eng" || $item1['lang']=="por" || $item1['lang']=="chi" ){
                            if ($item['visio_'.$item1['lang']] == "1") {
                                $article->setLanguages(
                                    array_unique(
                                        array_merge($article
                                        ->getLanguages(), [$currentLang])
                                    )
                                );
                            }
                        }
                    }
                    if ($item1['url_pdf'] ){
                        if (isset($processedAttachmentsMap[$oldArticleId][$item1['url_pdf']])) {
                            $resource = $processedAttachmentsMap[$oldArticleId][$item1['url_pdf']];
                            $resource->setLanguages(
                                array_unique(
                                    array_merge($resource->getLanguages(), [$currentLang])
                                )
                            );
                        }else{
                            $path = $item1['url_pdf'];
                            $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                            $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                            $path = self::SOURCE_DOMAIN.$path;
                            $attachment = $this->importFile('article', $path);
                            if ($attachment) {
                                $resource = new Resource();
                                $resource->setFile($attachment);
                                $resource->setFileName($attachment->getFileName());
                                $resource->setLanguages([$currentLang]);
                                $resource->setType('article_dossier');
                                $processedAttachmentsMap[$oldArticleId][$item1['url_pdf']] = $resource;
                            }
                        }
                    }
                }
            }
            if ($item['url_imagen'] ){
                if (isset($processedAttachmentsMap[$oldArticleId][$item['url_imagen']])) {
                    $resource = $processedAttachmentsMap[$oldArticleId][$item['url_imagen']];
                    $resource->setLanguages(
                        array_unique(
                            array_merge($resource->getLanguages(), [$currentLang])
                        )
                    );
                }else{
                    $path = $item['url_imagen'];
                    $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                    $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                    $path = self::SOURCE_DOMAIN.'/media_repository/gabinete/'.$path;
                    $attachment = $this->importFile('article', $path);
                    if ($attachment) {
                        $resource = new Resource();
                        $resource->setFile($attachment);
                        $resource->setFileName($attachment->getFileName());
                        $resource->setLanguages([$currentLang]);
                        $resource->setType('article_main_photo');
                        $processedAttachmentsMap[$oldArticleId][$item['url_imagen']] = $resource;
                    }
                }
            }
            if ($item['thumbnail']){
                if (isset($processedAttachmentsMap[$oldArticleId][$item['thumbnail']])) {
                    $resource = $processedAttachmentsMap[$oldArticleId][$item['thumbnail']];
                    $resource->setLanguages(
                        array_unique(
                            array_merge($resource->getLanguages(), [$currentLang])
                        )
                    );
                }else{
                    $path = $item['thumbnail'];
                    $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                    $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                    $path = self::SOURCE_DOMAIN.'/media_repository/OutputTumbs/'.$path;
                    $attachment = $this->importFile('article', $path);
                    if ($attachment) {
                        $resource = new Resource();
                        $resource->setFile($attachment);
                        $resource->setFileName($attachment->getFileName());
                        $resource->setLanguages([$currentLang]);
                        $resource->setType('article_thumbnail');
                        $processedAttachmentsMap[$oldArticleId][$item['thumbnail']] = $resource;
                    }
                }
            }
            // Adding the current instance to the offices mapping
            $processedArticleMap[$oldArticleId] = $article;
        // }
        }
        foreach ($processedArticleMap as $article) {
            // Persist only the registers with at least one active language
                $article->mergeNewTranslations();
                if (isset($processedAttachmentsMap[$article->getOldId()])) {
                    foreach ($processedAttachmentsMap[$article->getOldId()] as $key => $resource) {
                        $article->addAttachment($resource);
                    }
                }
                $this->em->persist($article);
                $this->em->flush();
                $this->logger->debug("Article ".$article->getId());
        }
    }
    // public function ArticlesupdatePublicationDate()
    // {
    //     $data = file_get_contents("JsonExports/Publicaciones.json");
    //     $items = json_decode($data, true);
    //     $articleRepository = $this->em->getRepository(Articles::class);
    //     foreach ($items as $item) {
    //         $articleId = $this->getMappedArticleId($item['id']);
    //         if ($articleId) {
    //             $article = $articleRepository->find($articleId);
    //             $article->setPublicationDate(new \DateTime($item['fecha_publicacion']));
    //             $this->em->persist($article);
    //             $this->em->flush();
    //         }else {
    //             $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
    //         }
    //     }
    // }
    public function ArticlesByLawyers()
    {
        $data = file_get_contents("JsonExports/PublicacionesAbogados.json");
        $items = json_decode($data, true);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $articleRepository = $this->em->getRepository(Articles::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [articles_lawyer]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " Lawyer:" . $item['abogado_id']);
            $articleId = $this->getMappedArticleId($item['publicacion_id']);
            $lawyerId = $this->getMappedLawyerId($item['abogado_id']);
            if ($articleId && $lawyerId) {
                $article = $articleRepository->find($articleId);
                $lawyer = $lawyerRepository->find($lawyerId);

                // $this->logger->debug("- Mapped article " . $article->translate("es")->getTitle());
                // $this->logger->debug("- Mapped lawyer " . $lawyer->translate("es")->getTitle());

                $article->addLawyer($lawyer);
                $this->em->persist($article);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function ArticlesByOffices()
    {
        $data = file_get_contents("JsonExports/PublicacionesOficina.json");
        $items = json_decode($data, true);
        $officeRepository = $this->em->getRepository(Office::class);
        $articleRepository = $this->em->getRepository(Articles::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [articles_office]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " office:" . $item['oficina_id']);
            $articleId = $this->getMappedArticleId($item['publicacion_id']);
            $oficceId = $this->getMappedOfficeId($item['oficina_id']);
            if ($articleId && $oficceId) {
                $article = $articleRepository->find($articleId);
                $office = $officeRepository->find($oficceId);

                // $this->logger->debug("- Mapped article " . $article->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $article->addOffice($office);
                $this->em->persist($article);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }
    public function ArticlesByActivities()
    {
        $data = file_get_contents("JsonExports/PublicacionesPractica.json");
        $items = json_decode($data, true);
        $activityRepository = $this->em->getRepository(Activity::class);
        $articleRepository = $this->em->getRepository(Articles::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [articles_activity]");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['publicacion_id'] . " practica:" . $item['practica_id']);
            $articleId = $this->getMappedArticleId($item['publicacion_id']);
            $practicaId = $this->getMappedActivityId($item['practica_id']);
            if ($articleId && $practicaId) {
                $article = $articleRepository->find($articleId);
                $practica = $activityRepository->find($practicaId);

                // $this->logger->debug("- Mapped article " . $article->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $article->addActivity($practica);
                $this->em->persist($article);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
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

    private function getMappedArticleId(?string $id): ?string
    {
        if (empty($this->mappedArticleIds)) {
            $this->loadMappedArticleIds();
        }
        return isset($this->mappedArticleIds[$id]) ? $this->mappedArticleIds[$id] : null;
    }

    private function loadMappedArticleIds()
    {
        $repository = $this->em->getRepository(Articles::class);
        $Articles = $repository->findAll();
        foreach ($Articles as $Article) {
            $this->mappedArticleIds[$Article
            ->getOldId()] = $Article->getId();
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

<?php
namespace App\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

use App\Entity\Activity;
use App\Entity\Desk;
use App\Entity\Event;
use App\Entity\Lawyer;
use App\Entity\Mention;
use App\Entity\Practice;
use App\Entity\Sector;
use App\Entity\Speaker;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private $container;
    private $logger;
    private $mappedLawyerIds;
    private $mappedEventIds;
    private $mappedActivityIds;

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
            $this->Mentions();
            $this->Events();
            $this->Activities();
            $this->SpeakersByEvent();
            $this->ActivitiesByEvent();
            $this->ActivitiesByLawyer();
        } else {
            switch ($table) {
                case "lawyer":
                    $this->Lawyers();
                    break;
                case "mentions":
                    $this->Mentions();
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
            }
        }
        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function Lawyers()
    {
        $data = file_get_contents("abogados.json");
        $items = json_decode($data, true);
        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE lawyer_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [Speaker]");
        $this->em->getConnection()->executeQuery("DELETE FROM [LawyerTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Lawyer]");

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

                // Temporal exclusion of photo field.
                // The new relation with the Resource Entity requires
                // a little more complex process to do the import
                // $lawyer->setPhoto($item['image']);

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

    public function Mentions()
    {
        $data = file_get_contents("abogados.json");
        $items = json_decode($data, true);
        $this->em->getConnection()->executeQuery("DELETE FROM [MentionTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Mention]");

        $lawyerRepository = $this->em->getRepository(Lawyer::class);

        $processedLawyersMap = [];

        foreach ($items as $item) {

            $oldLawyerId = $item['id_abogado'];
            $lawyerId = $this->getMappedLawyerId($oldLawyerId);

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if ($item['status']=='0' || $lawyerId == null || empty($item['menciones'])) {
                continue;
            }

            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current lawyer instance in a previous iteration ?
            if (isset($processedLawyersMap[$oldLawyerId])) {
                // in that case, restore it from $processedLawyersMap
                $lawyer = $processedLawyersMap[$oldLawyerId];
                $mention = $lawyer->getMentions()[0];
            }
            else {
                // in other case, restore it from the database
                $lawyer = $lawyerRepository->find($lawyerId);
                $mention = new Mention();
            }

            // Filling translatable fields
            $mention->translate($currentLang)->setBody($item['menciones']);
            $mention->mergeNewTranslations();
            $lawyer->addMention($mention);
            // Adding the current instance to map
            $processedLawyersMap[$oldLawyerId] = $lawyer;
        }

        foreach ($processedLawyersMap as $lawyer) {
            $mention = $lawyer->getMentions()[0];
            $this->em->persist($lawyer);
            $this->em->flush();
            $this->logger->debug("Mention ".$mention->getId()." - ".substr($mention->translate('es')->getBody(),0,50)."... [ Lawyer : ".$lawyer->getFullName() ." ]");
        }
    }

    public function Events()
    {
        $data = file_get_contents("eventos.json");
        $items = json_decode($data, true);

        $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE event_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM [EventTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Event]");

        $processedEventsMap = [];

        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if ($item['status']=='0' || $item['visible']=='0' || empty($item['titulo'])) {
                continue;
            }

            $oldEventId = $item['id'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current lawyer instance in a previous iteration ?
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

                // Temporal exclusion of attachment field.
                // The new attachments collections of files requires
                // a little more complex process to do the import
                //
                //$event->setAttachment($item['url_pdf']);

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
            // Filling translatable fields
            $event->translate($currentLang)->setTitle($item['titulo']);
            $event->translate($currentLang)->setDescription($item['resumen']);
            $event->translate($currentLang)->setSchedule($item['descripcion_lugar']);
            $event->translate($currentLang)->setProgram($item['programa']);
            $event->translate($currentLang)->setCustomCity($item['ciudad']);
            $event->translate($currentLang)->setCustomAddress($item['ubicacion_lugar']);
            // Adding the current instance to map
            $processedEventsMap[$oldEventId] = $event;
        }

        foreach ($processedEventsMap as $event) {
            // Persist only the registers with at least one active language
            if (!empty($event->getLanguages())) {
                // Persiste the instance
                $event->mergeNewTranslations();
                $this->em->persist($event);
                $this->em->flush();
                $this->logger->debug("Event ".$event->getId()." ".$event->translate('es')->getTitle());
            }
        }
    }

    public function Activities()
    {
        $data = file_get_contents("areas_practicas.json");
        $items = json_decode($data, true);

        $this->em->getConnection()->executeQuery("DELETE FROM [ActivityTranslation]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Activity]");

        $processedActivitiesMap = [];

        foreach ($items as $item) {

            // Has the current item the required conditions to be imported?
            // if not, Skip it !
            if (empty($item['titulo'])) {
                continue;
            }

            $oldActivityId = $item['id'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was processed the current lawyer instance in a previous iteration ?
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
                $activity->setImage($item['url_image']);
                $activity->setHighlighted(!(bool)$item['spractica']);
            }

            // Updating the languages field using the correspondent visio_* field
            if ($item['visio_'.$item['lang']] == "1") {
                $activity->setLanguages(
                    array_unique(
                        array_merge($activity->getLanguages(), [$currentLang])
                    )
                );
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
                // Persiste the instance
                $activity->mergeNewTranslations();
                $this->em->persist($activity);
                $this->em->flush();
                $this->logger->debug("Activity ".$activity->getId()." ".$activity->translate('es')->getTitle());
            }
        }
    }

    public function ActivitiesByLawyer()
    {
        $data = file_get_contents("abogadoArea.json");
        $items = json_decode($data, true);
        $lawyersMapping = [];
        $activitiesMapping = [];
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $activityRepository = $this->em->getRepository(Activity::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [lawyer_activity]");

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

                $lawyer->addActivity($activity);
                $this->em->persist($lawyer);
                $this->em->flush();

                // Adding lawyer and activity objects to the mapping arrays
                // in order to avoid ORM calls in each iteration
                $lawyersMapping[$item['id_abogado']] = $lawyer;
                $activitiesMapping[$item['id_area']] = $activity;
            }
            else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function ActivitiesByEvent()
    {
        $data = file_get_contents("eventosArea.json");
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
            }
            else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function SpeakersByEvent()
    {
        $data = file_get_contents("eventosPonente.json");
        $items = json_decode($data, true);
        $processedSpeakers = [];

        $eventRepository = $this->em->getRepository(Event::class);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);

        $this->em->getConnection()->executeQuery("DELETE FROM [event_speaker]");
        $this->em->getConnection()->executeQuery("DELETE FROM [Speaker]");

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
                    }
                    else {
                        $speaker = new Speaker();
                        $speaker->setOldId($item['id']);
                        $speaker->setLawyer($lawyer);
                    }

                    $event->addSpeaker($speaker);

                    $this->em->persist($event);
                    $this->em->flush();

                    $processedSpeakers[$item['id_abogado']] = $speaker;
                }
                else {
                    $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
                }
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
}

<?php
namespace App\Command;

use App\Entity\Activity;
use App\Entity\Event;
use App\Entity\Lawyer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct();
        $this->container = $container;
        $this->logger = $logger;
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

    protected function execute(InputInterface $input)
    {
        $this->logger->info('Empezando la migración...');
        $em = $this->container->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $table = $input->getOption('table');

        if ($table=="all") {
            $this->logger->info("Se van a exportar todas las tablas");
         //$this->Abogados($conn);
            // $this->Activity($conn);
             $this->Eventos($conn);
        } else {
            $this->logger->info("La tabla a exportar es : ".$table);
            switch ($table) {
                case "lawyer":
                    $this->Abogados($conn);
                    break;
                case "activity":
                    $this->Activity($conn);
                    break;
                case "event":
                    $this->Eventos($conn);
                    break;
            }
        }
        $this->logger->info("Se ha conectado con el servidor");
        $this->logger->info('Dia de la exportación '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function Abogados($conn)
    {
        $data = file_get_contents("abogados.json");
        $items = json_decode($data, true);
        $entityManager = $this->container->get('doctrine')->getManager();

        $conn->executeQuery("DELETE FROM [LawyerTranslation]");
        $conn->executeQuery("DELETE FROM [Lawyer]");

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
            }
            else {
                // in other case, create a new instance and fill it
                $lawyer = new Lawyer();
                $lawyer->setOldId($oldLawyerId);
                $lawyer->setName($item['nombre']);
                $lawyer->setSurname($item['apellidos']);
                $lawyer->setEmail($item['email']);
                $lawyer->setPhone(($item['telefono']));
                $lawyer->setFax(($item['fax']));
                $lawyer->setPhoto($item['image']);
                $lawyer->setLawyerType(
                    self::getMappedLawyerType($item['idtipoabogado'])
                );
            }
            // Updating the languages field using the correspondent visio_* field
            foreach (['esp','por','eng','chi'] as $lang) {
                if ($item['visio_'.$lang] == "1") {
                    $languages[] = self::getMappedLanguageCode($lang);
                }
            }
            // Filling translatable fields
            $lawyer->translate($currentLang)->setDescription($item['descripcion']);
            $lawyer->translate($currentLang)->setCurriculum($item['CV']);
            $lawyer->translate($currentLang)->setTraining($item['formacion']);
            // Adding the current instance to map
            $processedLawyersMap[$oldLawyerId] = $lawyer;
        }

        foreach ($processedLawyersMap as $lawyer) {
            $lawyer->mergeNewTranslations();
            $entityManager->persist($lawyer);
            $entityManager->flush();
        }

        return 0;
    }

    public function Eventos($conn)
    {
        $data = file_get_contents("eventos.json");
        $items = json_decode($data, true);
        $entityManager = $this->container->get('doctrine')->getManager();

        $conn->executeQuery("DELETE FROM [EventTranslation]");
        $conn->executeQuery("DELETE FROM [Event]");

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
            }
            else {
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
                $event->setAttachment($item['url_pdf']);
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
                        array_merge( $event->getLanguages(), [$currentLang])
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
                $entityManager->persist($event);
                $entityManager->flush();
                $this->logger->info("Event ".$event->getId()." ".$event->translate('es')->getTitle());
            }
        }
    }

    public function Activity($conn)
    {
        $data = file_get_contents("areas_practicas.json");
        $items = json_decode($data, true);
        $entityManager = $this->container->get('doctrine')->getManager();

        $conn->executeQuery("DELETE FROM [ActivityTranslation]");
        $conn->executeQuery("DELETE FROM [Activity]");

        $processedActivitysMap = [];

        foreach ($items as $item) {

            $oldActivityId = $item['id'];
            $currentLang = self::getMappedLanguageCode($item['lang']);

            // Was the current lawyer instance processed previously ?
            if (isset($processedActivitysMap[$oldActivityId])) {
                $activity = $processedActivitysMap[$oldActivityId];
                $activity->translate($currentLang)->setMetaTitle($item['titulo']);
                $activity->translate($currentLang)->getMetaDescription($item['resumen']);
                $processedActivitysMap[$oldActivityId] = $activity;
            } else {

                $activity = new Activity();
                $activity->setOldId($oldActivityId);
                $activity->setTitle((string)$item['url_pdf']);
                $activity->setBody((string)$item['contacto']);


                // TODO: validar estado de publicación en origen para reflajarlo en destino
                $activity->setLanguages(["es","en"]);
                $activity->setLocations(["es","uk"]);

                $activity->translate($currentLang)->setMetaTitle($item['titulo']);
                $activity->translate($currentLang)->getMetaDescription($item['resumen']);
                // Adding the current instance to map
                $processedActivitysMap[$oldActivityId] = $activity;
            }
        }

        foreach ($processedActivitysMap as $activity) {
            $activity->mergeNewTranslations();
            $entityManager->persist($activity);
            $entityManager->flush();
        }

        return 0;
    }

    public function Permisos($conn)
    {
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

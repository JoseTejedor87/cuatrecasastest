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
use App\Entity\Training;
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
use App\Entity\Page;
use App\Entity\Banner;
use App\Entity\Slider;
use App\Entity\Legislation;
use App\Entity\Region;

class ImportCommandDev extends Command
{
    protected static $defaultName = 'app:importToDev';
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
            $this->delTrainings();
            $this->delMentions();
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
            //      $this->Article();
            //      $this->ArticlesByActivities();
            //      $this->ArticlesByOffices();
            //      $this->ArticlesByLawyers();
            $this->ArticlesCategory();
            $this->ArticlesAuthors();
            $this->ArticlesPost();
            $this->ArticlesPostFiles();
            $this->ActivityActivities();
            $this->Mentions();
            $this->Trainings();
            $this->Pages();
            $this->videosNoticias();
            $this->VideoPublicationsByLawyers();
            $this->VideoPublicationsByOffices();
            $this->VideoPublicationsByActivities();
            $this->PublicationsByLegislation();

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
                case "regions":
                    $this->Regions();
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
                case "mentions":
                    $this->Mentions();
                break;
                case "trainings":
                    $this->Trainings();
                break;
                case "pages":
                    $this->Pages();
                break;

                case "banner":
                    $this->Banner();
                break;                
                case "videos":
                    $this->videosNoticias();
                break;
                case "video_lawyers":
                    $this->VideoPublicationsByLawyers();
                    break;
                case "video_offices":
                    $this->VideoPublicationsByOffices();
                    break;
                case "video_activities":
                    $this->VideoPublicationsByActivities();
                    break;
                case "legislation":
                    $this->PublicationsByLegislation();
                break;                                    
            }
        }
        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    
    //  $data = file_get_contents("JsonExports/legislacion.json");

    public function Legislation(){

        $data = file_get_contents("JsonExports/legislacion.json");
        $items = json_decode($data, true);


        $this->em->getConnection()->executeQuery("DELETE FROM Legislation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Legislation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([TrainingTranslation], RESEED, 1)");

        foreach ($items as $item) {
            $leg = new Legislation();
            $leg->setName($this->convertStringUTF8($item['nombre']));
            $this->em->persist($leg);
            $this->em->flush();
        }

    }

    public function PublicationsByLegislation()
    {
        $this->Legislation();
/*
        $data_ = file_get_contents("JsonExports/legislacion.json");
        $legislation = json_decode($data_, true);
*/
        $data = file_get_contents("JsonExports/PublicacionesLegislacion.json");
        $items = json_decode($data, true);
        $legislationRepository = $this->em->getRepository(Legislation::class);
        $publicationRepository = $this->em->getRepository(Publication::class);

        //$this->em->getConnection()->executeQuery("DELETE FROM publication_person");

        //  [{"publicacion_id":"90757","legislacion_id":"2"}
        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Publicacion:" . $item['publicacion_id'] . " legislacionID:" . $item['legislacion_id']);
            $publicationId = $this->getMappedPublicationId($item['publicacion_id']);
            $legislationId = $item['legislacion_id']; // En nuestra DB siempre tendra el id original 1, 2 y3 por eso se puede asi.

            
            if ($publicationId && $legislationId) {
                $publication = $publicationRepository->find($publicationId);
                $legislation = $legislationRepository->find($legislationId);
                $this->logger->debug("SEARCHED DATA: Publicacion:" . $publication->getId() . " legislacionID:" . $legislation->getId());
                $publication->addLegislation($legislation);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }    


    public function videosNoticias()
    {
        $pub_videos = json_decode(
            file_get_contents("JsonExports/Videos.json"),
            true
        );

        $pub_video_translations = json_decode(
            file_get_contents("JsonExports/VideosIdiomas.json"),
            true
        );

        $videos_idiom_processed = [];
        
        $this->logger->debug("videosNoticias Procesando array de idiomas...");

        $id_anterior = 0;
        $arrayVideoTrans = array();
        foreach ($pub_video_translations as $key => $item) {
            if ( $id_anterior != $item['videos_id']){
                $id_anterior = $item['videos_id'];
                $arrayVideoTrans[$item['videos_id']] = array();
            }
            array_push($arrayVideoTrans[$item['videos_id']],$item);
        }
        $this->logger->debug("array de arrayVideoTrans... creado por key = id_video");
        //print_r($arrayVideoTrans);die();

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/news-*"));

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($pub_videos as $key => $item) {
            $oldPublicationId = $item['id'];
            // create a new instance and fill it

            if(isset($processedPublicationMap[$oldPublicationId])){
                $publicationRepository = $this->em->getRepository(Publication::class);
                $publicationId = $this->getMappedPublicationId($oldPublicationId);
                if($publicationId){
                    $publication = $publicationRepository->find($publicationId); 
                }
            }else{
                if ($item['tipo_video']!=5) {
                    $publication = new News();
                } elseif ($item['tipo_video']==5) {
                    $publication = new Opinion();
                } else {
                    continue;
                }
            }
            if($publication){
                $publication->setOldId($oldPublicationId);
                $publication->setFeatured($item['destacada'] ? $item['destacada'] : 0);
                $publication->setFormat('video');

                $publication->setPublicationDate(new \DateTime($item['fecha_publicacion']));
                $arrayLang = [];
                $item['visio_es'] ? array_push($arrayLang, 'es') : '';
                $item['visio_en'] ? array_push($arrayLang, 'en') : '';
                $item['visio_pt'] ? array_push($arrayLang, 'pt') : '';
                $item['visio_cn'] ? array_push($arrayLang, 'zh') : '';
                array_unique($arrayLang);
                $publication->setLanguages($arrayLang);

                foreach ($arrayVideoTrans[$item['id']] as $key => $video_lan){             

                    $lang = $this->getMappedLanguageCodeById($video_lan['idiomas_id']);
                    if (isset($video_lan['title']) && $video_lan['title'] != ''){
                        $publication->translate($lang)->setTitle($this->convertStringUTF8($video_lan['title']));
                        $publication->translate($lang)->setContent($this->convertStringUTF8($video_lan['description']));
                        //$publication->setPublished(true);
                    } else {
                        $publication->translate($lang)->setTitle('Notitle');
                        $publication->translate($lang)->setContent($this->convertStringUTF8($video_lan['description']));
                        //$publication->setPublished(false);
                    }
                    
                    if ($item['url_source'] != '') {
                        $publication->setUrlVideo($item['url_source']);
                    }

                }
    
                if ($item['url_img']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['url_img']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['url_img']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['url_img']] = $resource;
                    } else {
                        $path = $item['url_img'];
                        $path = strpos($path, './') == 1 ? substr($path, 2) : $path;
                        $path = strpos($path, '/') != 0 ? ("/".$path) : $path;
                        $path = self::SOURCE_DOMAIN.'/media_repository/'.$path;
                        $attachment = $this->importFile('publication', $path);
                        if ($attachment) {
                            $resource = new Resource();
                            $resource->setFile($attachment);
                            $resource->setFileName($attachment->getFileName());
                            $resource->setLanguages(['es','en','pt','zh']);
                            $resource->setType('publication_main_photo');
                            $processedAttachmentsMap[$oldPublicationId][$item['url_img']] = $resource;
                        }
                    }
                }
                if ($item['url_thumb']) {
                    if (isset($processedAttachmentsMap[$oldPublicationId][$item['url_thumb']])) {
                        $resource = $processedAttachmentsMap[$oldPublicationId][$item['url_thumb']];
                        $resource->setLanguages(['es','en','pt','zh']);
                        $processedAttachmentsMap[$oldPublicationId][$item['url_thumb']] = $resource;
                    } else {
                        $path = $item['url_thumb'];
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
                            $processedAttachmentsMap[$oldPublicationId][$item['url_thumb']] = $resource;
                        }
                    }
                }
				

                $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                $this->logger->debug("New Publication : From $oldPublicationId ~> To ".$publication->getId());

                // Adding the current instance to the offices mapping
                $processedPublicationMap[$oldPublicationId] = $publication;
            }
        }

    }
 

    public function VideoPublicationsByLawyers()
    {
        $data = file_get_contents("JsonExports/VideosAbogados.json");
        $items = json_decode($data, true);
        $lawyerRepository = $this->em->getRepository(Lawyer::class);
        $publicationRepository = $this->em->getRepository(Publication::class);
        $personRepository = $this->em->getRepository(Person::class);

        //$this->em->getConnection()->executeQuery("DELETE FROM publication_person");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['videos_id'] . " Lawyer:" . $item['abogado_id']);
            $publicationId = $this->getMappedPublicationId($item['videos_id']);
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
                self::setRegions($publication);
                $this->em->persist($publication);
                $this->em->flush();
                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped lawyer " . $lawyer->translate("es")->getTitle());
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function VideoPublicationsByOffices()
    {
        $data = file_get_contents("JsonExports/VideosOficina.json");
        $items = json_decode($data, true);
        $officeRepository = $this->em->getRepository(Office::class);
        $publicationRepository = $this->em->getRepository(Publication::class);

        //$this->em->getConnection()->executeQuery("DELETE FROM publication_office ");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['videos_id'] . " office:" . $item['oficina_id']);
            $publicationId = $this->getMappedPublicationId($item['videos_id']);
            $oficceId = $this->getMappedOfficeId($item['oficina_id']);
            if ($publicationId && $oficceId) {
                $publication = $publicationRepository->find($publicationId);
                $office = $officeRepository->find($oficceId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addOffice($office);
                self::setRegions($publication);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    public function VideoPublicationsByActivities()
    {
        $data = file_get_contents("JsonExports/VideosPractica.json");
        $items = json_decode($data, true);
        $activityRepository = $this->em->getRepository(Activity::class);
        $publicationRepository = $this->em->getRepository(Publication::class);

        //$this->em->getConnection()->executeQuery("DELETE FROM publication_activity ");

        foreach ($items as $item) {
            $this->logger->debug("ORIGINAL DATA: Article:" . $item['videos_id'] . " practica:" . $item['practica_id']);
            $publicationId = $this->getMappedPublicationId($item['videos_id']);
            $practicaId = $this->getMappedActivityId($item['practica_id']);
            if ($publicationId && $practicaId) {
                $publication = $publicationRepository->find($publicationId);
                $practica = $activityRepository->find($practicaId);

                // $this->logger->debug("- Mapped article " . $publication->translate("es")->getTitle());
                // $this->logger->debug("- Mapped office " . $office->translate("es")->getTitle());

                $publication->addActivity($practica);
                self::setRegions($publication);
                $this->em->persist($publication);
                $this->em->flush();
            } else {
                $this->logger->warning(">>>>>>>>>>>>>>>> SKIPPED !!!!");
            }
        }
    }

    
    public function delTrainings(){
        $this->em->getConnection()->executeQuery("DELETE FROM Training ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Training AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Training], RESEED, 1)");      

        $this->em->getConnection()->executeQuery("DELETE FROM TrainingTranslation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE TrainingTranslation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([TrainingTranslation], RESEED, 1)");
    }

    public function delMentions(){
        $this->em->getConnection()->executeQuery("DELETE FROM Mention ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Mention AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Mention], RESEED, 1)");        
        $this->em->getConnection()->executeQuery("DELETE FROM MentionTranslation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE MentionTranslation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([MentionTranslation], RESEED, 1)");
    }

    public function Trainings(){
        $this->logger->debug("La tabla Lawyer debe estar previamente cargada y correcta para que las relaciones esten bien ");
        $data = file_get_contents("JsonExports/abogados.json");
        $items = json_decode($data, true);
     
        $this->delTrainings();


        foreach ($items as $key => $item) {
            
            if($item['formacion'] == '') continue;
            $currentLang = self::getMappedLanguageCode($item['lang']);
            $lawyerRepository = $this->em->getRepository(Lawyer::class);
            

            $lawyerId = $this->getMappedLawyerId($item['id_abogado']);
            if($lawyerId == '') {
                $this->logger->debug("============================= SKIPPED ======================================================== "); 
                $this->logger->debug("============================= No se encontro lawyer en el repo con OLD ID : ".$item['id_abogado']); 
                continue;
            }
            $this->logger->debug("lawyerId ".$lawyerId); 
            $lawyer = $lawyerRepository->find($lawyerId);

            if($lawyer == '') {
                $this->logger->debug("============================= FALLO en la busqueda No se encontro lawyer en el repo "); 
                continue;
            }
            $this->logger->debug(" lawyer: ".$lawyer->getName());

            $training = new Training();
            
                if(preg_match('/Languages/',$item['formacion'])){
                    $matches = explode("Languages", $item['formacion']);
                }else{
                    if(preg_match('/Language/',$item['formacion'])){
                        $matches = explode("Language", $item['formacion']);
                    }
                }
                if(preg_match('/Idiomas/',$item['formacion'])){
                    $matches = explode("Idiomas", $item['formacion']);
                }else{
                    if(preg_match('/Idioma/',$item['formacion'])){
                        $matches = explode("Idioma", $item['formacion']);
                    }
                }
                if(isset($matches)){

                    //  UPDATE the  knownLanguages of Lawyer  Table
                    $delimiter = array(" y ", " e ", " and ");
                    $languages = str_replace($delimiter, " , ", strip_tags($matches[1]));
                    $arrayCharacterToQuit = [": ", ".", " ", "&nbsp;", "\\r\\n&nbsp;"];
                    $languages = str_replace($arrayCharacterToQuit, "", $languages);
                    $languageArray = explode(",", $languages);

                    // JUST UPDATE IF knowledge_languages is empty
                    if ( $lawyer->getKnownLanguages() == '[]' || $item['lang'] == 'eng'){
                        // Save from format ["Spanish", "English"] to ['es', 'en']
                        $languageArrayCoded = [];
                        foreach($languageArray as $language){
                            $lan = self::getMappedLanguageParser($language);
                            if (is_null($lan)) continue ;
                            array_push($languageArrayCoded,$lan);
                        }

                        if ( !empty($languageArrayCoded)){
                            $lawyer->setKnownLanguages($languageArrayCoded);
                            $lawyer->mergeNewTranslations();
                            self::setRegions($lawyer);
                            $this->em->persist($lawyer);
                            $this->em->flush();
                        }

                    }
                    
                    // separar los registro en diferente al </br></br>

                    $trainingsArray = explode("<br /><br />",str_replace(["<p>","</p>"], "",$matches[0]));
                    $json = json_encode($trainingsArray);

                    foreach($trainingsArray as $item_training){
                        if ($item_training != ''){
                            $training->translate($currentLang)->setDescription($item_training);
                            $training->setLawyer($lawyer);            
                            $training->mergeNewTranslations();
                            $this->em->persist($training);
                            $this->em->flush();
                            $this->logger->debug("training ".$training->getId()." ".$training->translate($currentLang)->getDescription());         
                                
                        }
                    }
                    $this->logger->debug("string ".$matches[0]); 
                    $this->logger->debug("trainingsArray ".$json); 

                }

        }

    }

    public function Mentions(){
        $this->logger->debug("La tabla Lawyer debe estar previamente cargada y correcta para que las relaciones esten bien ");
        $data = file_get_contents("JsonExports/abogados.json");
        $items = json_decode($data, true);
        $this->delMentions();

        foreach ($items as $item) {
            
            if($item['menciones'] == '') continue;
            $currentLang = self::getMappedLanguageCode($item['lang']);
            $lawyerRepository = $this->em->getRepository(Lawyer::class);

            $lawyerId = $this->getMappedLawyerId($item['id_abogado']);
            if($lawyerId == '') {
                $this->logger->debug("============================= SKIPPED ======================================================== "); 
                $this->logger->debug("============================= No se encontro lawyer en el repo con OLD ID : ".$item['id_abogado']); 
                continue;
            }
            $this->logger->debug("lawyerId ".$lawyerId); 
            $lawyer = $lawyerRepository->find($lawyerId);

            if($lawyer == '') {
                $this->logger->debug("============================= FALLO en la busqueda No se encontro lawyer en el repo "); 
                continue;
            }
            $this->logger->debug(" lawyer: ".$lawyer->getName());


            $mention = new Mention();
            $mention->translate($currentLang)->setDescription($item['menciones']);
            $mention->setLawyer($lawyer);
            
            $mention->mergeNewTranslations();
            $this->em->persist($mention);
            $this->em->flush();
            $this->logger->debug("Mention ".$mention->getId()." ".$mention->translate($currentLang)->getDescription());         
     
        }

    }

    public function Pages(){
        $this->em->getConnection()->executeQuery("DELETE FROM Page ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Page AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Page], RESEED, 1)");      

        $this->em->getConnection()->executeQuery("DELETE FROM PageTranslation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE PageTranslation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([PageTranslation], RESEED, 1)");
        $data = file_get_contents("JsonExports/pages.json");
        $items = json_decode($data, true);

        foreach($items as $item){
            $page = new Page();
            $page->setLanguages($item['lenguaje']);
            $page->setCustomTemplate($item['custom_template']);
            self::setRegions($page);
            foreach($item['lenguaje'] as $currentLang){
                $page->translate($currentLang)->setTitle($item['titulo']);
            }
            $page->mergeNewTranslations();
            $this->em->persist($page);
            $this->em->flush();
            $this->logger->debug("Page ".$page->getId());
        }

    }

    public function Banner(){

        ///  Si la tabla ya existe hay que borrar la foreign key de Resources 

        $this->em->getConnection()->executeQuery("DELETE FROM Banner ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Banner AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Banner], RESEED, 1)");      

        $this->em->getConnection()->executeQuery("DELETE FROM SliderTranslation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE SliderTranslation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([SliderTranslation], RESEED, 1)");

        $this->em->getConnection()->executeQuery("DELETE FROM Slider ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Slider AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Slider], RESEED, 1)");

        $this->em->getConnection()->executeQuery("DELETE FROM slider_banner ");
        $this->em->getConnection()->executeQuery("ALTER TABLE slider_banner AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([slider_banner], RESEED, 1)");
        $data = file_get_contents("JsonExports/banner.json");
        $items = json_decode($data, true);

        foreach($items as $item){
            $banner = new Banner();
            $banner->setLocation($item['location']);
            $banner->setDelay($item['delay']);
            $banner->setSpeed($item['speed']);
            
            foreach($item['sliders'] as $item_slide){
                $slide = new Slider();
                $slide->translate('es')->setTitle($item_slide['titulo']);
                $slide->translate('es')->setDescription($item_slide['description']);
                $slide->translate('es')->setUrl($item_slide['url']);
                $slide->setPriority($item_slide['priority']);
                
                $photo = $this->importFile('slider', $item_slide['image']);
                if ($photo) {
                    $resource = new Resource();
                    $resource->setFile($photo);
                    $resource->setFileName($photo->getFileName());
                    $resource->setSlider($slide);
                    $slide->setImage($resource);
                }

                $slide->mergeNewTranslations();
                $this->em->persist($slide);
                $this->em->flush();
    
                $banner->addSlider($slide);

            }
            $this->em->persist($banner);
            $this->em->flush();
            $this->logger->debug("Banner ".$banner->getId());           

        }

    }


    public function Lawyers()
    {
        $data = file_get_contents("JsonExports/abogados.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/lawyer-*"));

        // Removing registers from database
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Lawyer], RESEED, 1)");
        // $this->em->getConnection()->executeQuery("DELETE FROM Office ");
        $this->em->getConnection()->executeQuery("DELETE FROM Resource WHERE lawyer_id IS NOT NULL");
        //      $this->em->getConnection()->executeQuery("DELETE FROM publication_person ");
        $this->em->getConnection()->executeQuery("DELETE FROM event_person ");
        $this->em->getConnection()->executeQuery("DELETE FROM Person ");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Person], RESEED, 1)");
        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_secondary_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM LawyerTranslation ");
        $this->em->getConnection()->executeQuery("DELETE FROM Lawyer ");        
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Lawyer], RESEED, 1)");



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
                $startDate = \DateTime::createFromFormat('Y-m-d G:i:s.u', $item['fecha_modificacion']);
                $lawyer->setCreatedAt(
                    $startDate ? $startDate : date("Y-m-d H:i:s")
                );                

                $lawyer->setInitials(trim($item['siglas']));

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
            self::setRegions($lawyer);
            // Filling translatable fields
            $lawyer->translate($currentLang)->setDescription($item['descripcion']);
            $lawyer->translate($currentLang)->setCurriculum($item['CV']);
            // $Training = $item['formacion'];

            $processedLawyersMap[$oldLawyerId] = $lawyer;
        }

        foreach ($processedLawyersMap as $lawyer) {
            $lawyer->mergeNewTranslations();
            $this->em->persist($lawyer);
            $this->em->flush();
            // $this->logger->debug("Lawyer ".$lawyer->getOldId()." - ".$lawyer->getId()." ".$lawyer->getFullName());
            $this->logger->debug("Region ".json_encode($lawyer->getRegions()));
        }
    }


    public function Events()
    {
        $data = file_get_contents("JsonExports/eventos.json");
        $items = json_decode($data, true);

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/event-*"));

        $this->em->getConnection()->executeQuery("DELETE FROM Resource WHERE event_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM event_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM event_person ");
        $this->em->getConnection()->executeQuery("DELETE FROM EventTranslation ");
        $this->em->getConnection()->executeQuery("DELETE FROM Event ");

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
                self::setRegions($event);
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

        $this->em->getConnection()->executeQuery("DELETE FROM activity_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM activity_activity_parents ");
        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_secondary_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM event_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM ActivityTranslation ");
        $this->em->getConnection()->executeQuery("DELETE FROM Activity ");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Activity], RESEED, 1)");

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
                self::setRegions($activity);
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

        $this->em->getConnection()->executeQuery("DELETE FROM activity_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM activity_activity_parents ");
        
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
                self::setRegions($activity);
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
                self::setRegions($activity);
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

        $this->em->getConnection()->executeQuery("DELETE FROM QuoteTranslation");
        $this->em->getConnection()->executeQuery("DELETE FROM Quote");
        # $this->em->getConnection()->executeQuery("DBCC CHECKIDENT (Quote, RESEED, 1)");

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

        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_activity ");
        $this->em->getConnection()->executeQuery("DELETE FROM lawyer_secondary_activity ");

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
                self::setRegions($lawyer);
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

        $this->em->getConnection()->executeQuery("DELETE FROM event_activity ");

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
                self::setRegions($event);
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

        $this->em->getConnection()->executeQuery("DELETE FROM event_person ");
        // $this->em->getConnection()->executeQuery("DELETE FROM person ");
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
                    self::setRegions($event);
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

        $this->em->getConnection()->executeQuery("DELETE FROM Resource WHERE office_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM OfficeTranslation ");
        $this->em->getConnection()->executeQuery("DELETE FROM Office ");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Office], RESEED, 1)");

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
            self::setRegions($office);
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
                self::setRegions($event);
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

        $this->em->getConnection()->executeQuery("DELETE FROM Resource WHERE award_id IS NOT NULL");
        $this->em->getConnection()->executeQuery("DELETE FROM AwardTranslation ");
        $this->em->getConnection()->executeQuery("DELETE FROM Award ");

        $this->em->getConnection()->executeQuery("ALTER TABLE Award AUTO_INCREMENT = 1");
        $this->em->getConnection()->executeQuery("ALTER TABLE AwardTranslation AUTO_INCREMENT = 1");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Award], RESEED, 1)");
        //      $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([AwardTranslation], RESEED, 1)");

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
                    
                    if (preg_match("/\b\d{4}\b/", $item['otorgado']  , $matches)) {
                        $Date = \DateTime::createFromFormat('Y-m-d', $matches[0].'-01-01');
                        $Award->setYear(
                            $Date ? $Date : date("Y-m-d")
                        );
                    } else{
                        $Date = \DateTime::createFromFormat('Y-m-d', date("Y-m-d"));
                        $Award->setYear(
                            $Date ? $Date : date("Y-m-d")
                        );
                    }
                    

                }
                // Updating the languages field using the correspondent visio_* field
                /*
                if ($item['visio_'.$item['lang']] == "1") {
                    $Award->setLanguages(
                        array_unique(
                            array_merge($Award
                        ->getLanguages(), [$currentLang])
                        )
                    );
                }
                */
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

                // Adding the current instance to the events mapping
                $processedAwardsMap[$oldAwardId] = $Award;
            }
        }

        foreach ($processedAwardsMap as $Award) {
            // Persist only the registers with at least one active language
            //if (!empty($Award->getLanguages())) {
                // Persist the instance
                $Award->mergeNewTranslations();
                $this->em->persist($Award);
                $this->em->flush();
                $this->logger->debug("Award ".$Award->getId()." ".$Award->translate('es')->getTitle());
            //}
        }
    }


    public function Regions()
    {
        $this->em->getConnection()->executeQuery("DELETE FROM Region ");
        $this->em->getConnection()->executeQuery("ALTER TABLE Region AUTO_INCREMENT = 1");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Page], RESEED, 1)");

        $this->em->getConnection()->executeQuery("DELETE FROM RegionTranslation ");
        $this->em->getConnection()->executeQuery("ALTER TABLE RegionTranslation AUTO_INCREMENT = 1");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([PageTranslation], RESEED, 1)");
        $data = file_get_contents("JsonExports/regions.json");
        $items = json_decode($data, true);

        foreach ($items as $item) {
            $Region = new Region();
            $Region->setLanguages($item['lenguaje']);
            $Region->setPrincipal(isset($item['principal']) ? $item['principal'] : 0);
            self::setRegions($Region);
            foreach ($item['lenguaje'] as $currentLang) {
                $Region->translate($currentLang)->setTitle($item['titulo']);
                $Region->translate($currentLang)->setSummary($item['summary']);
                $Region->translate($currentLang)->setContent($item['content']);
            }
            $Region->setPublished(true);
            $Region->mergeNewTranslations();
            $this->em->persist($Region);
            $this->em->flush();
            $this->logger->debug("Region ".$Region->getId());
        }

        // INSERT INTO `region_office` VALUES (1,3),(1,4),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(1,12),(1,14),(1,15),(1,16),(1,17),(1,23),(2,2),(2,21),(3,24),(3,27),(3,28),(3,37),(3,38),(4,18),(5,22),(6,20),(7,1),(7,35),(8,19),(8,25),(8,26);


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

        // $this->em->getConnection()->executeQuery("DELETE FROM [Resource] WHERE publication_id IS NOT NULL");
        // $this->em->getConnection()->executeQuery("DELETE FROM [ArticleTranslation]");
        // $this->em->getConnection()->executeQuery("DELETE FROM [Article]");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Article], RESEED, 1)");

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($publications as $key => $item) {
            $oldPublicationId = $item['id'];
            // create a new instance and fill it

            if ($item['tipo_publicacion']==3 || $item['tipo_publicacion']==4 || $item['tipo_publicacion']==5 || $item['tipo_publicacion']==6 || $item['tipo_publicacion']==7) {
                $publication = new Opinion();
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

        $this->em->getConnection()->executeQuery("DELETE FROM publication_person");

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
                self::setRegions($publication);
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

        $this->em->getConnection()->executeQuery("DELETE FROM publication_office ");

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
                self::setRegions($publication);
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

        $this->em->getConnection()->executeQuery("DELETE FROM publication_activity ");

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
                self::setRegions($publication);
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
                        self::setRegions($ArticleCategory);
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
                            self::setRegions($ArticleCategory);
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
                            $article = new Opinion();
                            $article->setOldId($post['id']);
                            //$article->setStatus($post['status']=='publish' ? 1 : 0);
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
                            self::setRegions($article);
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
                            $article = new Opinion();
                            $article->setOldId($post['id']);
                            //    $article->setStatus($post['status']=='publish' ? 1 : 0);
                            
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
                            self::setRegions($article);
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
                            $resource->setType('publication_main_photo');
                            $articulo->addAttachment($resource);
                            self::setRegions($articulo);
                            $this->em->persist($articulo);
                            $this->em->flush();
                        }
                    }
                }
            }
        }
    }

    public function News_BKP()
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

        // $this->em->getConnection()->executeQuery("DELETE FROM Resource WHERE publication_id IS NOT NULL");
        // $this->em->getConnection()->executeQuery("DELETE FROM ArticleTranslation ");
        // $this->em->getConnection()->executeQuery("DELETE FROM Article ");
        // $this->em->getConnection()->executeQuery("DBCC CHECKIDENT ([Article], RESEED, 1)");

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($publications as $key => $item) {
            
            // BY DANIEL FRENAR PARA QUE NO HAGA TODO EL CICLO
            if( $key == 100) break;

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

        // Removing files from disk
        $resources_path = $this->container->getParameter('kernel.project_dir').'/public'.$this->container->getParameter('app.path.uploads.resources');
        array_map('unlink', glob($resources_path."/news-*"));

        $processedPublicationMap = [];
        $processedAttachmentsMap = [];

        foreach ($publications as $key => $item) {
            $oldPublicationId = $item['id'];
            // create a new instance and fill it

            if(isset($processedPublicationMap[$oldPublicationId])){
                $publicationRepository = $this->em->getRepository(Publication::class);
                $publicationId = $this->getMappedPublicationId($oldPublicationId);
                if($publicationId){
                    $publication = $publicationRepository->find($publicationId); 
                }
            }else{
                if ($item['tipo_noticia']==3) {
                    $publication = new News();
                } elseif ($item['tipo_noticia']==4) {
                    $publication = new News();
                } elseif ($item['tipo_noticia']==5) {
                    $publication = new Opinion();
                } else {
                    continue;
                }
            }
            if($publication){
                $publication->setOldId($oldPublicationId);
                $publication->setFeatured($item['destacada'] ? $item['destacada'] : 0);
                $publication->setPublicationDate(new \DateTime($item['fecha_publicacion']));
				$currentLang = self::getMappedLanguageCode($item['lang']);
                if ($currentLang && isset($item['title']) && $item['title'] != '') {

                    $publication->translate($currentLang)->setTitle($this->convertStringUTF8($item['title']));
                    $publication->translate($currentLang)->setSummary($this->convertStringUTF8($item['summary']));
                    $publication->translate($currentLang)->setContent($this->convertStringUTF8($item['contenido']));

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

        // Attaching translations
        foreach ($publication_translations as $index => $item1) {
            $oldPublicationId = $item1['noticias_id'];
            $publicationRepository = $this->em->getRepository(Publication::class);
            $publicationId = $this->getMappedPublicationId($oldPublicationId);
            if($publicationId){
                $publication = $publicationRepository->find($publicationId); 
            }else{
                continue;
            }
            // $publication = isset($processedPublicationMap[$oldPublicationId]) ? $processedPublicationMap[$oldPublicationId] :  null;
            if ($publication) {
                $currentLang = self::getMappedLanguageCodeById($item1['idiomas_id']);
                if ($currentLang && isset($item1['title']) && $item1['title'] != '') {

                    $publication->translate($currentLang)->setTitle($this->convertStringUTF8($item['title']));
                    $publication->translate($currentLang)->setSummary($this->convertStringUTF8($item['summary']));
                    $publication->translate($currentLang)->setContent($this->convertStringUTF8($item['contenido']));

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


                // Is the last item in the collection
                if ($index+1 == count($publication_translations)) {
                    if (!empty($publication->getLanguages())) {
                        self::setRegions($publication);
                        $this->persistPublication($publication, $processedAttachmentsMap[$publication->getOldId()] ?? []);
                        $this->logger->debug("Updating Publication ".$publication->getId());
                    }
                }
            }
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
                self::setRegions($publication);
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
                self::setRegions($publication);
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
                self::setRegions($publication);
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
                self::setRegions($publication);
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

    private static function getMappedLanguageParser(?string $code): ?string
    {
        $map = [
            'Spanish'		=> 'es',
            'English'		=> 'en',
            'French'		=> 'fr',
            'Catalan'		=> 'ca',
            'Chinese'		=> 'zh',
            'Portuguese'	=> 'pt',
            'German'		=> 'ge',
            'Italian'		=> 'it',
            'Basque'		=> 'va',
            'Dutch'			=> 'ho',
        ];
        return isset($map[$code]) ? $map[$code] : null;
    }

    private static function setRegions($entity)
    {
        $arrayLang = $entity->getLanguages();
        $arrayRegions = [];
        foreach ($arrayLang as $key => $lang) {
            if ( $lang == 'es' )  array_push($arrayRegions,'latam','spain');
            if ( $lang == 'pt' )  array_push($arrayRegions,'portugal');
            if ( $lang == 'en' )  array_push($arrayRegions,'global');
        }
        $entity->setRegions($arrayRegions);
        return $arrayRegions;
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

    private static function convertStringUTF8($input){

        //$input = str_replace("Â¢", "¢",$input);
        $input = str_replace("Â£", "£",$input);
        $input = str_replace("Â¥", "¥",$input);
        //$input = str_replace("Â¨", "¨",$input);
        $input = str_replace("Â©", "©",$input);
        $input = str_replace("Âª", "ª",$input);
        //$input = str_replace("Â«", "«",$input);
        //$input = str_replace("Â", "",$input);
        //$input = str_replace("Â­", "­",$input);
        $input = str_replace("Â®", "®",$input);
        //$input = str_replace("Â¯", "¯",$input);
        //$input = str_replace("Â°", "°",$input);
        //$input = str_replace("Â±", "±",$input);
        $input = str_replace("Â²", "²",$input);
        $input = str_replace("Â³", "³",$input);
        $input = str_replace("Â´", "´",$input);
        $input = str_replace("Âµ", "µ",$input);
        //$input = str_replace("Â", "",$input);
        //$input = str_replace("Â·", "·",$input);
        //$input = str_replace("Â¸", "¸",$input);
        //$input = str_replace("Â¹", "¹",$input);
        $input = str_replace("Âº", "º",$input);
        $input = str_replace("Â»", "»",$input);
        $input = str_replace("Â¼", "¼",$input);
        $input = str_replace("Â½", "½",$input);
        $input = str_replace("Â¾", "¾",$input);
        $input = str_replace("Â¿", "¿",$input);
        $input = str_replace("Ã€", "À",$input);
        $input = str_replace("Ã‚", "Â",$input);
        $input = str_replace("Ãƒ", "Ã",$input);
        $input = str_replace("Ã„", "Ä",$input);
        $input = str_replace("Ã…", "Å",$input);
        $input = str_replace("Ã†", "Æ",$input);
        $input = str_replace("Ã‡", "Ç",$input);
        $input = str_replace("Ãˆ", "È",$input);
        $input = str_replace("Ã‰", "É",$input);
        $input = str_replace("ÃŠ", "Ê",$input);
        $input = str_replace("Ã‹", "Ë",$input);
        $input = str_replace("ÃŒ", "Ì",$input);
        $input = str_replace("Ã", "Í",$input);
        $input = str_replace("ÃŽ", "Î",$input);
        $input = str_replace("Ã", "Ï",$input);
        //$input = str_replace("Ã", "Ð",$input);
        $input = str_replace("Ã‘", "Ñ",$input);
        $input = str_replace("Ã’", "Ò",$input);
        $input = str_replace("Ã“", "Ó",$input);
        $input = str_replace("Ã”", "Ô",$input);
        $input = str_replace("Ã•", "Õ",$input);
        $input = str_replace("Ã–", "Ö",$input);
        //$input = str_replace("Ã—", "×",$input);
        //$input = str_replace("Ã˜", "Ø",$input);
        $input = str_replace("Ã™", "Ù",$input);
        $input = str_replace("Ãš", "Ú",$input);
        $input = str_replace("Ã›", "Û",$input);
        $input = str_replace("Ãœ", "Ü",$input);
        //$input = str_replace("Ã", "Ý",$input);
        $input = str_replace("Ãž", "Þ",$input);
        $input = str_replace("ÃŸ", "ß",$input);
        $input = str_replace("Ã", "à",$input);
        $input = str_replace("Ã¡", "á",$input);
        $input = str_replace("Ã¢", "â",$input);
        $input = str_replace("Ã£", "ã",$input);
        $input = str_replace("Ã¤", "ä",$input);
        //$input = str_replace("Ã¥", "å",$input);
        //$input = str_replace("Ã¦", "æ",$input);
        $input = str_replace("Ã§", "ç",$input);
        $input = str_replace("Ã¨", "è",$input);
        $input = str_replace("Ã©", "é",$input);
        $input = str_replace("Ãª", "ê",$input);
        $input = str_replace("Ã«", "ë",$input);
        $input = str_replace("Ã", "ì",$input);
        $input = str_replace("Ã­", "í",$input);
        $input = str_replace("Ã®", "î",$input);
        $input = str_replace("Ã¯", "ï",$input);
        //$input = str_replace("Ã°", "ð",$input);
        $input = str_replace("Ã±", "ñ",$input);
        $input = str_replace("Ã²", "ò",$input);
        $input = str_replace("Ã³", "ó",$input);
        $input = str_replace("Ã´", "ô",$input);
        //$input = str_replace("Ãµ", "õ",$input);
        $input = str_replace("Ã", "ö",$input);
        //$input = str_replace("Ã·", "÷",$input);
        //$input = str_replace("Ã¸", "ø",$input);
        $input = str_replace("Ã¹", "ù",$input);
        $input = str_replace("Ãº", "ú",$input);
        $input = str_replace("Ã»", "û",$input);
        $input = str_replace("Ã¼", "ü",$input);
        $input = str_replace("Ã½", "ý",$input);
        //$input = str_replace("Ã¾", "þ",$input);
        $input = str_replace("Ã¿", "ÿ",$input);



        $input = str_replace("ã§", "ç",$input);
        $input = str_replace("ã©", "é",$input);
        $input = str_replace("ã©", "é",$input);
        $input = str_replace("ã¡", "á",$input);
        $input = str_replace("ã³", "ó",$input);
        $input = str_replace("ã", "í",$input);
        $input = str_replace("ãº", "ú",$input);
        //ã

        $input = str_replace("à§", "ç",$input);
        $input = str_replace("à©", "é",$input);
        $input = str_replace("à©", "é",$input);
        $input = str_replace("à¡", "á",$input);
        $input = str_replace("à³", "ó",$input);

        $input = str_replace("àº", "ú",$input);
        $input = str_replace("à¼", "ü",$input);
        
        

     
    

        $_encoding = mb_detect_encoding($input, "UTF-8, ISO-8859-1");
        return html_entity_decode($input, ENT_QUOTES | ENT_HTML5, $_encoding);

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

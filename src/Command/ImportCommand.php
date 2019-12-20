<?php
namespace App\Command;
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
                    new InputOption('table', 'a', InputOption::VALUE_REQUIRED,"Si table esta vacio, se ejecutará para todas las tablas","all"),
                ))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Empezando la migración',// A line
            '======================',// Another line
        ]);
        $em = $this->container->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $table = $input->getOption('table');

        if($table=="all"){
            $output->writeln("Se van a exportar todas las tablas");
            // $this->Abogados($conn);
            // $this->Activity($conn);
            // $this->Eventos($conn,$output);
            
        }else{
            $output->writeln("La tabla a exportar es : ".$table); 
            switch ($table) {
                case "lawyer":
                    $this->Abogados($conn);
                    break;
                case "activity":
                    $this->Activity($conn);
                    break;
                case "event":
                    $this->Eventos($conn,$output);
                    break;
            }
            
        }
        $output->writeln("Se ha conectado con el servidor");
        $this->logger->info('Dia de la exportación'.date("Y-m-d H:i:s"));
        return 0;
    }

    public function Abogados($conn){
        $data = file_get_contents("abogados.json");
        $abogados = json_decode($data, true);
        $entityManager = $this->container->get('doctrine')->getManager();
        $platform   = $conn->getDatabasePlatform();

        $conn->executeQuery("DELETE FROM [LawyerTranslation]
        DBCC CHECKIDENT ('[web_cuatrecasas_cms_Symfony].[dbo].[LawyerTranslation]',RESEED, 0)");
        $conn->executeQuery("DELETE FROM [Person]
        DBCC CHECKIDENT ('[web_cuatrecasas_cms_Symfony].[dbo].[Person]',RESEED, 0)");
         $idViejo = -1;
         foreach ($abogados as $key => $abogado) {
             $idNuevo = $abogado['id_abogado'];
             if($key != 0){
                 $idViejo = $abogados[$key-1]['id_abogado'];
             }
             if($idNuevo != $idViejo){
                 echo(var_dump($abogado));
                 $lawyer = new Lawyer();
                 $lawyer->setUserId($abogado['siglas']);
                 $lawyer->setName($abogado['nombre']);
                 $lawyer->setSurname($abogado['apellidos']);
                 $lawyer->setEmail($abogado['email']);
                 $lawyer->setPhone(($abogado['telefono']));
                 $lawyer->setFax(($abogado['fax']));
                 $lawyer->setPhoto($abogado['image']);
                 $lawyer->setLawyerType($abogado['idtipoabogado']);
                 $lawyer->setStatus($abogado['status']);
                 foreach ($abogados as $key1 => $abogado1) {
                     if($abogado1['id_abogado'] == $abogado['id_abogado']){
                         switch ($abogado1['lang']) {
                             case "esp":
                                 $lang = 'es';
                                 break;
                             case 'eng':
                                 $lang = 'en';
                                 break;
                             case 'por':
                                 $lang = 'pt';
                                 break;
                             default:
                                 $lang = $abogado1['lang'];
                         }
                         $lawyer->translate($lang)->setDescription($abogado1['descripcion']);
                         $lawyer->translate($lang)->setCv($abogado1['CV']);
                         $lawyer->translate($lang)->setExperience($abogado1['experiencia']);
                         $lawyer->translate($lang)->setTags($abogado1['tags']);
                         $lawyer->translate($lang)->setTraining($abogado1['formacion']);
                         $lawyer->translate($lang)->setMentions($abogado1['menciones']);
                     }
                 }
                 $lawyer->mergeNewTranslations();
                 $entityManager->persist($lawyer);
                 $entityManager->flush();  
             }
         }    
        return 0;
    }

    public function Eventos($conn,$output){

    }
    public function Activity($conn){

    }

    public function Permisos($conn){



    }






}
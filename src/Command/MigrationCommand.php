<?php
namespace App\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;

class MigrationCommand extends Command
{
    protected static $defaultName = 'app:migration';
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct();
        $this->container = $container;
        $this->tables = ['Usuarios','Permisos','Abogados','Eventos','Sectores','Servicios'];
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
        $em = $this->container->get('doctrine')->getManager('customer');
        $conn = $em->getConnection();
        $table = $input->getOption('table');

        if($table=="all"){
            $output->writeln("Se van a exportar todas las tablas");
            $this->Abogados($conn);
            $this->Activity($conn);
            $this->Eventos($conn,$output);
            
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
 //Fecha de la exportacion, total de registros, tipo de registro, ruta del json y errores
        return 0;
    }

    public function Abogados($conn){
        $query = "SELECT a.*, o.* FROM abogado a inner JOIN abogado_desc o ON o.id_abogado = a.id order by a.id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('abogados.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla abogados');
        $this->logger->info('Se ha guardado con el nombre abogados.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        // telefono 
        // fax 
        // imagen
        // tipo de agogado
        //fecha de creacion  ( doctrine Behaviors ) no importar
        //fecha de actualizacion  ( doctrine Behaviors ) no importar
        //fecha de publicacion creacion (preguntar) y el status 
        //omitimos visionado idiomas
        //telefono coleccion de string  o fill

        return 0;
    }

    public function Eventos($conn,$output){
        $query = "SELECT [id] ,[lang] ,[titulo] ,[resumen] ,[fecha_inicio] ,[fecha_final] ,[url_pdf] ,[email] ,[lugar] ,[mapa] ,[rss] ,[twitter] ,[facebook] ,[url_friend] ,[tags] ,[status] ,[ciudad] ,[principal] ,[image] ,[url_video] ,[url_inscripcion] ,[descripcion_lugar] ,[ubicacion_lugar] ,[contacto] ,[telefono] ,[programa] ,[Notificado] ,[fechaNotificacion] ,[destacada] ,[image_slider] ,[tipo] ,[visible] ,[aforo] ,[image_mail] ,[restricted]  FROM eventos order by id";        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('eventos.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla eventos');
        $this->logger->info('Se ha guardado con el nombre eventos.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function Activity($conn){
        $query = "SELECT [id] ,[lang] ,[titulo] ,[descripcion] ,[experiencia] ,[tags] ,[url_friend] ,[id_area] ,[url_image] ,[quote] ,[spractica] ,[sap]  FROM areas_practicas";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('areas_practicas.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla areas_practicas');
        $this->logger->info('Se ha guardado con el nombre areas_practicas.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;

    // activity
    // - sectorial
    // - legal
    }

    public function Permisos($conn){



    }






}
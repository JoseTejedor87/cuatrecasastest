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
        $em = $this->container->get('doctrine')->getManager('default');
        $conn = $em->getConnection();
        $table = $input->getOption('table');


        if($table=="all"){
            $output->writeln("Se van a exportar todas las tablas");
        }else{
            $output->writeln("La tabla a exportar es : ".$table); 
        }

        $output->writeln("Se ha conectado con el servidor");
        $query = "SELECT * FROM page WHERE id = 1";
        $stmt = $conn->prepare($query);
        //EXECUTE DA UN LOG
        if($conn->isConnected()){
            $output->writeln("se ha conectado" . var_dump($conn->isConnected()));
            $stmt->execute();
            $results = $stmt->fetchAll();
            $output->writeln("Este es el resultado del id = 1 : ". var_dump($results));
        }

        $this->logger->info('Error log');
        $this->logger->error('HOLA');

    }

    public function Usuarios($conn){

        //tabla User 
        //tabla Abogado 
        //columna userID JTEB (preguntar si los abogados tienen)
        //fecha de creacion  ( doctrine Behaviors )
        //fecha de actualizacion  ( doctrine Behaviors )
        //Roles ( Dependencia )
        //nombre
        // apellido
        // email


    }
    public function Roles($conn){
        // Siempre borrar todos los datos
        // [tb_roles]
        // role id 
        // role desc
        // desc
    }
    public function Abogados($conn){
        // telefono 
        // fax 
        // imagen
        // tipo de agogado
        //fecha de creacion  ( doctrine Behaviors ) no importar
        //fecha de actualizacion  ( doctrine Behaviors ) no importar
        //fecha de publicacion (preguntar) y el status 
        //omitimos visionado idiomas
        //telefono coleccion de string  o fill


    }

    public function Permisos($conn){



    }

    public function Eventos($conn){

    }
    public function Sectores($conn){

    }
    public function Servicios($conn){

    }



}
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
use Symfony\Component\HttpFoundation\Response;
use App\Repository\SOAPContactsClientRepository;
use App\Controller\SOAPContactsClientController;



class SoapCommand extends Command
{
    protected static $defaultName = 'app:Soap';
    private $container;
    private $logger;
    private $url;
    private $conn;
    private $CrearTablas;
    private $SOAPContactsClientRepository;
    private $soap;

    const SOURCE_DOMAIN = "https://www.cuatrecasas.com";

    public function __construct(ContainerInterface $container, LoggerInterface $logger,SOAPContactsClientRepository $SOAPContactsClientRepository)
    {
        parent::__construct();
        $this->container = $container;
        $this->logger = $logger;
        $this->CrearTablas = false;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->SOAPContactsClientRepository = $SOAPContactsClientRepository;
        $this->soap  = new SOAPContactsClientController;

    }

    protected function configure()
    {
        $this->url = 'http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl';
        $this
            // the short description shown while running "php bin/console list"
            ->setHelp("Este comando sirve para migrar de la BBDD Gestor contactos")
            // the full command description shown when running the command with
            ->setDescription('Este comando sirve para migrar de la BBDD Gestor contactos')
            // Set options
            ->setDefinition(
                new InputDefinition()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Empezando la migración...');
        $this->logger->info("Se van a importar todas las tablas");
        $em = $this->container->get('doctrine');
        $this->conn = $em->getConnection();
        if($this->CrearTablas){ 
            $this->SOAPContactsClientRepository->deleteTables();
            $this->SOAPContactsClientRepository->createTables();
        }else{
            $this->SOAPContactsClientRepository->deleteTables();
        }
       
        $this->getPaises(); 
        $this->getProvincias(); 
        $this->getIdiomas(); 
        $this->getAreasInteres();
        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function getPaises()
    {
        $res  = array ('filter' =>( array ( 'IdPais' => "", 'Nombre'  =>  "")));
        $data = $this->soap->getPaises($res);
        $this->SOAPContactsClientRepository->setPaises($data);
           
    }

    public function getProvincias()
    {

            $res  = array ('filter' =>( array ( 'LanguageId' => "", 'PaisId'  =>  "", 'ProvinciaId'  =>  '')));
            $data = $this->soap->getProvincias($res);
            $this->SOAPContactsClientRepository->setProvincias($data);
           
    }
    public function getIdiomas()
    {
      
        $res  = array ('filter' =>( array ( 'IdiomaId' => "", 'LanguageId'  =>  "")));
        $data = $this->soap->getIdiomas($res);
        $this->SOAPContactsClientRepository->setIdiomas($data);
        
    }   
    public function getAreasInteres()
    {
        
        $res  =  array ('filter' =>( array ( 'AreaInteresId' => "", 'LanguageId'  => "")));
        $data = $this->soap->getAreasInteres($res);
        $this->SOAPContactsClientRepository->setAreasInteres($data);
    }
    public function getOficinas()
    {
        
        $res  = array ('filter' =>( array ( 'OficinaId' => "")));
        $data = $this->soap->getOficinas($request);
        $this->SOAPContactsClientRepository->setOficinas($data);
    }
}

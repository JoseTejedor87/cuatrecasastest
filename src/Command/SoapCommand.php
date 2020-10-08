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
        $em = $this->container->get('doctrine');
        $this->conn = $em->getConnection();
        if($this->CrearTablas){ 
            //$this->SOAPContactsClientRepository->deleteTables();
            $this->SOAPContactsClientRepository->createTables();
        }else{
            $this->SOAPContactsClientRepository->truncateTables();
        }
       
        $this->getPaises(); 
        $this->getProvincias(); 
        $this->getIdiomas(); 
        $this->getAreasInteres();

        $this->getOficinas(); 
        $this->getSecretarias(); 
        $this->getResponsablesMarketing(); 
        $this->getSociosResponsables();
        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function getPaises()
    {
        $data = $this->soap->getPaises("");
        $this->SOAPContactsClientRepository->setPaises(json_decode($data->getContent()));
           
    }

    public function getProvincias()
    {
            $data = $this->soap->getProvincias("","");
            $this->SOAPContactsClientRepository->setProvincias(json_decode($data->getContent()));
           
    }
    public function getIdiomas()
    {
        $data = $this->soap->getIdiomas();
        $this->SOAPContactsClientRepository->setIdiomas(json_decode($data->getContent()));
        
    }   
    public function getAreasInteres()
    {
        $data = $this->soap->getAreasInteres();
        $this->SOAPContactsClientRepository->setAreasInteres(json_decode($data->getContent()));
    }
    public function getOficinas()
    {
        $data = $this->soap->getOficinas();
        $this->SOAPContactsClientRepository->setOficinas(json_decode($data->getContent()));
    }
    public function getSecretarias()
    {
        $data = $this->soap->getSecretarias();
        $this->SOAPContactsClientRepository->setSecretarias(json_decode($data->getContent()));
    }
    public function getResponsablesMarketing()
    {
        $data = $this->soap->getResponsablesMarketing();
        $this->SOAPContactsClientRepository->setResponsablesMarketings(json_decode($data->getContent()));
    }
    public function getSociosResponsables()
    {
        $data = $this->soap->getSociosResponsables();
        $this->SOAPContactsClientRepository->setSociosResponsables(json_decode($data->getContent()));
    }
}

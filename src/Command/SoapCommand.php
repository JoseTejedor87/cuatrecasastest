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



class SoapCommand extends Command
{
    protected static $defaultName = 'app:Soap';
    private $container;
    private $logger;
    private $url;
    private $conn;
    private $CrearTablas;

    const SOURCE_DOMAIN = "https://www.cuatrecasas.com";

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct();
        $this->container = $container;
        $this->logger = $logger;
        $this->CrearTablas = false;
        $this->em = $this->container->get('doctrine')->getManager();
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
        $this->getAreasInteres(); 

        $this->logger->info('Fin de importación :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function getPaises()
    {

            $client = new \SoapClient($this->url);
            $res  = $client->GetPaises( array ('filter' =>( array ( 'LanguageId' => '', 'PaisId'  =>  ''))));
            $data = $res->GetPaisesResult->PaisWebDto;
            if($this->CrearTablas){
                $query = "CREATE TABLE GC_paises (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    IdPais VARCHAR(30) NOT NULL,
                    Nombre VARCHAR(150) NOT NULL
                    )";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
            $values = "";
            foreach ($data as $key => $value) {
                if($key==0){
                    $values = $values . '("'.$value->IdPais.'","'.$value->Nombre.'")';
                }else{
                    $values = $values . ',("'.$value->IdPais.'","'.$value->Nombre.'")';
                }
                
            }
            $query = "INSERT INTO 
            GC_paises(IdPais, Nombre)
            VALUES
            ".$values.";";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    }

    public function getProvincias()
    {
            $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
            $res  = $client->GetProvincias( array ('filter' =>( array ( 'LanguageId' => "", 'PaisId'  =>  "", 'ProvinciaId'  =>  ''))));
            $data = $res->GetProvinciasResult->ProvinciaWebDto;
            if($this->CrearTablas){
                $query = "CREATE TABLE GC_provincias (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    IdPais VARCHAR(30) NOT NULL,
                    IdProvincia VARCHAR(30) NOT NULL,
                    Nombre VARCHAR(150) NOT NULL
                    )";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
            $values = "";
            foreach ($data as $key => $value) {
                if($key==0){
                    $values = $values . '("'.$value->IdPais.'","'.$value->IdProvincia.'","'.$value->Nombre.'")';
                }else{
                    $values = $values . ',("'.$value->IdPais.'","'.$value->IdProvincia.'","'.$value->Nombre.'")';
                }
                
            }
            $query = "INSERT INTO 
            GC_provincias(IdPais, IdProvincia , Nombre)
            VALUES
            ".$values.";";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    }
    public function getIdiomas()
    {
        $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
        $res  = $client->GetIdiomas( array ('filter' =>( array ( 'IdiomaId' => "", 'LanguageId'  =>  ""))));
        $data = $res->GetIdiomasResult->IdiomaDto;
        if($this->CrearTablas){
            $query = "CREATE TABLE GC_idiomas (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                IdIdioma VARCHAR(30) NOT NULL,
                Nombre VARCHAR(150) NOT NULL
                )";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->IdIdioma.'","'.$value->Nombre.'")';
            }else{
                $values = $values . ',("'.$value->IdIdioma.'","'.$value->Nombre.'")';
            }
            
        }
        $query = "INSERT INTO 
        GC_idiomas(IdIdioma , Nombre)
        VALUES
        ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }   
    public function getAreasInteres()
    {
        $client = new \SoapClient('http://gestorcontactosdev.cuatrecasas.com/GestorContactosWcfService.svc?wsdl');
        $res  = $client->GetAreasInteres( array ('filter' =>( array ( 'AreaInteresId' => "", 'LanguageId'  => ""))));
        $data = $res->GetAreasInteresResult->AreaInteresWebDto;
        if($this->CrearTablas){
            $query = "CREATE TABLE GC_areasInteres (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                IdAreaInteres VARCHAR(30) NOT NULL,
                Nombre VARCHAR(150) NOT NULL
                )";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->IdAreaInteres.'","'.$value->Nombre.'")';
            }else{
                $values = $values . ',("'.$value->IdAreaInteres.'","'.$value->Nombre.'")';
            }
            
        }
        $query = "INSERT INTO GC_areasInteres(IdAreaInteres, Nombre) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    }

}

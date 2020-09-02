<?php

namespace App\Repository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


class SOAPContactsClientRepository extends ServiceEntityRepository
{
    private $container;
    private $em;
    private $conn;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->conn = $this->em->getConnection();
    }


    public function createTables()
    {
        
        $query = "CREATE TABLE GC_paises (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            IdPais VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_provincias (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            IdPais VARCHAR(30) NOT NULL,
            IdProvincia VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_idiomas (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            IdIdioma VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_areasInteres (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            IdAreaInteres VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_oficinas (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            OficinaId VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_secretarias (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            EmpId VARCHAR(30) NOT NULL,
            Iniciales VARCHAR(150) NOT NULL,
            Nombre VARCHAR(150) ,
            Apellidos VARCHAR(150) ,
            Email VARCHAR(150) ,
            Telefono VARCHAR(150) 
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_responsablesMarketings (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            EmpId VARCHAR(30) NOT NULL,
            Iniciales VARCHAR(150) NOT NULL,
            Nombre VARCHAR(150) ,
            Apellidos VARCHAR(150) ,
            Email VARCHAR(150) ,
            Telefono VARCHAR(150) 
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_sociosResponsables (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            EmpId VARCHAR(30) NOT NULL,
            Iniciales VARCHAR(150) NOT NULL,
            Nombre VARCHAR(150) ,
            Apellidos VARCHAR(150) ,
            Email VARCHAR(150) ,
            Telefono VARCHAR(150) 
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();


        
        
        return true;
    }
    public function deleteTables()
    {
        
        $query = "DROP TABLE GC_paises";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "DROP TABLE GC_provincias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "DROP TABLE GC_idiomas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "DROP TABLE GC_areasInteres";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $query = "DROP TABLE GC_oficinas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        
        $query = "DROP TABLE GC_secretarias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        
        $query = "DROP TABLE GC_responsablesMarketings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "DROP TABLE GC_sociosResponsables";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return true;
    }
    public function truncateTables()
    {
        
        $query = "TRUNCATE TABLE GC_paises";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_provincias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_idiomas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_areasInteres";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_oficinas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_secretarias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        

        $query = "TRUNCATE TABLE GC_responsablesMarketings";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "TRUNCATE TABLE GC_sociosResponsables";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return true;
    }
    public function setPaises($data)
    {
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
        
        return true;
    }
    
    public function setProvincias($data)
    {
        
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
        
        return true;
    }
    
    public function setIdiomas($data)
    {
        
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
        
        return true;
    }

    public function setAreasInteres($data)
    {
        
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
        
        return true;
    }

    public function setOficinas($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->IdOficina.'","'.$value->Nombre.'")';
            }else{
                $values = $values . ',("'.$value->IdOficina.'","'.$value->Nombre.'")';
            }
            
        }
        $query = "INSERT INTO GC_oficinas(OficinaId, Nombre) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }

    public function setSecretarias($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }else{
                $values = $values . ',("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }
            
        }
        $query = "INSERT INTO GC_secretarias(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }

    public function setResponsablesMarketings($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }else{
                $values = $values . ',("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }
            
        }
        $query = "INSERT INTO GC_responsablesMarketings(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }

    public function setSociosResponsables($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . '("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }else{
                $values = $values . ',("'.$value->EmpId.'","'.$value->Iniciales.'","'.$value->Nombre.'","'.$value->Apellidos.'","'.$value->Email.'","'.$value->Telefono.'")';
            }
            
        }
        $query = "INSERT INTO GC_sociosResponsables(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }

}
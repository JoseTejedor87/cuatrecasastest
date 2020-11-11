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
            id int   PRIMARY KEY AUTO_INCREMENT,
            IdPais VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_provincias (
            id INT  PRIMARY KEY AUTO_INCREMENT,
            IdPais VARCHAR(30) NOT NULL,
            IdProvincia VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_idiomas (
            id INT  PRIMARY KEY AUTO_INCREMENT,
            IdIdioma VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_areasInteres (
            id INT   PRIMARY KEY AUTO_INCREMENT,
            IdAreaInteres VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_oficinas (
            id INT   PRIMARY KEY AUTO_INCREMENT,
            OficinaId VARCHAR(30) NOT NULL,
            Nombre VARCHAR(150) NOT NULL
            )";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "CREATE TABLE GC_secretarias (
            id INT   PRIMARY KEY AUTO_INCREMENT,
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
            id INT  PRIMARY KEY AUTO_INCREMENT,
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
            id INT  PRIMARY KEY AUTO_INCREMENT,
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
                    $values = $values . "('".$value->IdPais."','".str_replace("'","",$value->Nombre) ."')";
                }else{
                    $values = $values . ",('".$value->IdPais."','".str_replace("'","",$value->Nombre)."')";
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
    public function getPaises()
    {
        $sql = "SELECT * FROM GC_paises";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    
    public function setProvincias($data)
    {
        
        $values = "";
        $values2 = "";
        $values3 = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                if($key<1000){
                    $values = $values . ",('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
                } 
            }
            if($key==1000){
                $values2 = $values2 . "('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                if($key<2000 && $key>1000)
                $values2 = $values2 . ",('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
            } 
            if($key==2000){
                $values3 = $values3 . "('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                if($key<3000 && $key>2000)
                $values3 = $values3 . ",('".$value->IdPais."','".str_replace("'","",$value->IdProvincia)."','".str_replace("'","",$value->Nombre) ."')";
            } 

        }
        $query = "INSERT INTO 
        GC_provincias (IdPais, IdProvincia , Nombre)
        VALUES
        ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "INSERT INTO 
        GC_provincias (IdPais, IdProvincia , Nombre)
        VALUES
        ".$values2.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();


        
        return true;
    }
    public function getProvincias()
    {
        $sql = "SELECT * FROM GC_provincias";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    
    public function setIdiomas($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->IdIdioma."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                $values = $values . ",('".$value->IdIdioma."','".str_replace("'","",$value->Nombre) ."')";
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
    public function getIdiomas()
    {
        $sql = "SELECT * FROM GC_idiomas";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    public function setAreasInteres($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->IdAreaInteres."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                $values = $values . ",('".$value->IdAreaInteres."','".str_replace("'","",$value->Nombre) ."')";
            }
            
        }
        $query = "INSERT INTO GC_areasInteres(IdAreaInteres, Nombre) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    public function getAreasInteres()
    {
        $sql = "SELECT * FROM GC_areasInteres";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    public function setOficinas($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->IdOficina."','".str_replace("'","",$value->Nombre) ."')";
            }else{
                $values = $values . ",('".$value->IdOficina."','".str_replace("'","",$value->Nombre) ."')";
            }
            
        }
        $query = "INSERT INTO GC_oficinas(OficinaId, Nombre) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    public function getOficinas()
    {
        $sql = "SELECT * FROM GC_areasInteres";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    public function setSecretarias($data)
    {
        
        $values = "";
        $values2 = "";
        $values3 = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }else{
                if($key<1000){
                $values = $values . ",('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
                }
            }
            if($key==1000){
                $values2 = $values2 . "('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }else{
                if($key<2000 && $key>1000)
                $values2 = $values2 . ",('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            } 
            if($key==2000){
                $values3 = $values3 . "('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }else{
                if($key<3000 && $key>2000)
                $values3 = $values3 . ",('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }
            
        }
       
        $query = "INSERT INTO GC_secretarias(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "INSERT INTO GC_secretarias(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values2.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $query = "INSERT INTO GC_secretarias(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values3.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    public function getSecretarias()
    {
        $sql = "SELECT * FROM GC_secretarias";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    public function setResponsablesMarketings($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }else{
                $values = $values . ",('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }
            
        }
        $query = "INSERT INTO GC_responsablesMarketings(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    public function getResponsablesMarketings()
    {
        $sql = "SELECT * FROM GC_responsablesMarketings";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
    public function setSociosResponsables($data)
    {
        
        $values = "";
        foreach ($data as $key => $value) {
            if($key==0){
                $values = $values . "('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }else{
                $values = $values . ",('".$value->EmpId."','".$value->Iniciales."','".$value->Nombre."','".$value->Apellidos."','".$value->Email."','".$value->Telefono."')";
            }
            
        }
        $query = "INSERT INTO GC_sociosResponsables(EmpId, Iniciales,Nombre,Apellidos,Email,Telefono) VALUES ".$values.";";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return true;
    }
    public function getSociosResponsables()
    {
        $sql = "SELECT * FROM GC_sociosResponsables";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $ValuesO =$stmt->fetchAll();    
        
        
        return $ValuesO;
    }
}

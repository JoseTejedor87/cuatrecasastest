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
use App\Entity\User;



class UsersCommand extends Command
{
    protected static $defaultName = 'app:Users';
    private $container;
    private $logger;
    private $url;
    private $conn;
    private $CrearTablas;
    private $soap;
    private $em;
    private $emUser;
    private $connUser;
    const SOURCE_DOMAIN = "https://www.cuatrecasas.com";

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct();
        $this->container = $container;
        $this->logger = $logger;
        $this->em =  $this->container->get('doctrine')->getManager();
        $this->conn = $this->em->getConnection();
        $this->emUser =  $this->container->get('doctrine')->getManager('customeruser');
        $this->connUser = $this->emUser->getConnection();
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
        $this->UpdateUsers(); 
        $this->UpdateRoles(); 
        $this->logger->info('Fin de actualización :: '.date("Y-m-d H:i:s"));
        return 0;
    }

    public function UpdateUsers()
    {
        $query = "Select distinct shortname from SAP.dbo.tb_user_list_prod_Active";
        $stmt = $this->connUser->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $userRepository = $this->em->getRepository(User::class);
        foreach ($results as $key => $value) {
            $user = $userRepository->findOneBy(['user_id' => $value['shortname']]);
            if(!$user){
                $user = new User();
                $user->setUserId($value['shortname']);
                $user->setRoles(["Norole"]);
                $this->em->persist($user);
                $this->em->flush();
            }
        }
           
    }
    public function UpdateRoles()
    {
        $userRepository = $this->em->getRepository(User::class);
        $users = $userRepository->findAll();
        foreach ($users as $key => $user) {
            $idUser = $user->getUserId();
            if( $idUser !== 'JMMA' && $idUser !== 'JTEB'){
                $query = "select distinct SHORTNAME, CATEGORYID, CATEGORY, AREAID, AREANAME, ADMINGROUPID, ADMINGROUPNAME, P_PO as colectivo from SAP.dbo.tb_user_list_prod_Active where SHORTNAME like '".$idUser."' AND (AREAID like 'GD' or AREAID like 'RH' or AREAID like 'GF' or AREAID like 'AC' or ADMINGROUPID like '917' or ADMINGROUPID like '949-EXTS')";
                $stmt = $this->connUser->prepare($query);
                $stmt->execute();
                $resultsAdmin = $stmt->fetch();
                if($resultsAdmin){             
                    if (!in_array("ROLE_ADMIN", $user->getRoles())) {
                        if(in_array("Norole", $user->getRoles())){
                            $user->setRoles(["ROLE_ADMIN"]);
                        }else{
                            $user->setRoles(
                                array_unique(
                                    array_merge($user->getRoles(), ["ROLE_ADMIN"])
                                )
                            );
                        }
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                }
                $query = "select distinct SHORTNAME, CATEGORYID, CATEGORY, AREAID, AREANAME, ADMINGROUPID, ADMINGROUPNAME, P_PO as colectivo from [SAP].[dbo].[tb_user_list_prod_Active] where SHORTNAME like '".$idUser."' AND (CATEGORYID like '%SC' or CATEGORYID like '%SC1' or CATEGORYID like '%SC3' or CATEGORYID like '%SC5' or CATEGORYID like '%SC6' or CATEGORYID like '%SC8' or CATEGORYID like '%SC9')";
                $stmt = $this->connUser->prepare($query);
                $stmt->execute();
                $resultsUsers = $stmt->fetch();
                if($resultsUsers){
                    
                    if (!in_array("ROLE_USER", $user->getRoles())) {
                        if(in_array("Norole", $user->getRoles())){
                            $user->setRoles(["ROLE_USER"]);
                        }else{
                            $user->setRoles(
                                array_unique(
                                    array_merge($user->getRoles(), ["ROLE_USER"])
                                )
                            );
                        }
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                    
                }
                
            }
            
        }
    }

   
}

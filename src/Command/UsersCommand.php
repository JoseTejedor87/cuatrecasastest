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
            $query = "Select * from SAP.dbo.tb_user_list_prod_Active";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $user = $userRepository->findOneBy(['user_id' => $value['shortname']]);
            if(!$user){
                $user = new User();
                $user->setUserId($value['shortname']);
                $user->setRoles(["test_user"]);
                $this->em->persist($user);
                $this->em->flush();
            }
        }
           
    }

   
}

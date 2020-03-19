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
                case "eventArea":
                    $this->EventosArea($conn,$output);
                    break;
                case "eventosPonente":
                    $this->EventosPonente($conn,$output);
                    break;
                case "abogadoArea":
                    $this->AbogadoArea($conn,$output);
                    break;
                case "areasQuotes":
                    $this->AreasQuotes($conn,$output);
                    break;
                case "premios":
                    $this->Premios($conn,$output);
                    break;
                case "oficinas":
                    $this->Oficinas($conn,$output);
                    break;
                case "oficinaDescripcion":
                    $this->OficinaDescripcion($conn,$output);
                    break;
                case "oficinaAbogado":
                    $this->OficinaAbogado($conn,$output);
                    break;
                case "noticias":
                    $this->Noticias($conn,$output);
                    break;     
                case "noticiasIdioma":
                    $this->NoticiasIdioma($conn,$output);
                    break;  
            } 
        }
        $output->writeln("Se ha conectado con el servidor");
        $this->logger->info('Dia de la exportación'.date("Y-m-d H:i:s"));
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
        return 0;
    }
    public function Eventos($conn,$output){
        $query = "SELECT [id] ,[lang] ,[titulo] ,[resumen] ,[fecha_inicio] ,[fecha_final] ,[url_pdf] ,[email] ,[lugar] ,[mapa] ,[rss] ,[twitter] ,[facebook] ,[url_friend] ,[tags] ,[status] ,[ciudad] ,[principal] ,[image] ,[url_video] ,[url_inscripcion] ,[descripcion_lugar] ,[ubicacion_lugar] ,[contacto] ,[telefono] ,[programa] ,[Notificado] ,[fechaNotificacion] ,[destacada] ,[image_slider] ,[tipo] ,[visible] ,[aforo] ,[image_mail] ,[restricted], [status],[visio_esp] ,[visio_por] ,[visio_eng], [visio_chi], [url_friend]  FROM eventos order by id";        $stmt = $conn->prepare($query);
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
        $query = "SELECT [id] ,[lang] ,[titulo] ,[descripcion] ,[experiencia] ,[tags] ,[url_friend] ,[id_area] ,[url_image] ,[quote] ,[spractica] ,[sap] ,[visio_esp] ,[visio_por] ,[visio_eng], [visio_chi], [url_friend] FROM areas_practicas";
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
    public function EventosArea($conn,$output){
        $query = "SELECT [id_evento] ,[id_area] ,[id_area_sub]  FROM eventos_area";        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('eventosArea.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla eventos_area');
        $this->logger->info('Se ha guardado con el nombre eventosArea.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function EventosPonente($conn,$output){
        $query = "SELECT [id] ,[id_evento] ,[lang] ,[nombre] ,[apellidos] ,[cargo] ,[telefono] ,[email] ,[descripcion] ,[image] ,[link] ,[id_abogado] ,[empresa] ,[orden] ,[hash]  FROM eventos_ponente";        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('eventosPonente.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla eventos_Ponente');
        $this->logger->info('Se ha guardado con el nombre eventosPonente.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    } 
    public function AbogadoArea($conn,$output){
        $query = "SELECT  [id_abogado] ,[id_area] ,[id_area_sub] ,[principal] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[abogado_area]";       
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('abogadoArea.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla abogado_area');
        $this->logger->info('Se ha guardado con el nombre abogadoArea.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function AreasQuotes($conn,$output){
        $query = "SELECT  [id_area] ,[id_quote] ,[lang] ,[quote_text]  ,[orden] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[areas_quotes]";       
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('areasQuotes.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla areas_quotes');
        $this->logger->info('Se ha guardado con el nombre areasQuotes.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function Premios($conn,$output){
        $query = "SELECT [id] ,[lang] ,[title] ,[otorgado] ,[fecha]  ,[desc_award] ,[desc_award_firma] ,[desc_award_indiv] ,[tags] ,[url_image] ,[url_friend] ,[destacado] ,[posicion] ,[rss] ,[facebook] ,[twitter] ,[visio_esp]  ,[visio_por] ,[visio_eng] ,[status] ,[otorgado_a] ,[orden] ,[visio_chi] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[premios]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('premios.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla premios');
        $this->logger->info('Se ha guardado con el nombre premios.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function Oficinas($conn,$output){
        $query = "SELECT [id] ,[ciudad] ,[direccion] ,[cp] ,[pais] ,[contacto] ,[url_friend] ,[email] ,[fax] ,[tel] ,[img_map] ,[link_google] ,[img_office] ,[visio_esp] ,[visio_por] ,[visio_eng] ,[facebook] ,[twitter] ,[rss] ,[status] ,[lugar] ,[ciudad_por] ,[ciudad_eng] ,[visio_chi] ,[zonageografica] ,[sap] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[oficina]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('oficinas.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla oficinas');
        $this->logger->info('Se ha guardado con el nombre oficinas.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function OficinaDescripcion($conn,$output){
        $query = "SELECT [id] ,[id_oficina] ,[lang] ,[descripcion] ,[tags] ,[ciudad] ,[pais] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[oficina_desc]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('OficinaDescripcion.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla OficinaDesc');
        $this->logger->info('Se ha guardado con el nombre OficinaDescripcion.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function OficinaAbogado($conn,$output){
        $query = "SELECT [id_abogado] ,[id_oficina] ,[id_direccion]  ,[director] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[oficina_abogado]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('OficinaAbogado.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla OficinaAbogado');
        $this->logger->info('Se ha guardado con el nombre OficinaAbogado.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function Noticias($conn,$output){
        $query = "SELECT [id] ,[lang] ,[title] ,[summary] ,[contenido] ,[medio] ,[tipo_noticia] ,[fecha_noticia] ,[fecha_modificacion] ,[fecha_publicacion] ,[url_pdf] ,[url_imagen] ,[url_link] ,[url_friend] ,[url_video] ,[url_podcast] ,[tags] ,[status] ,[rss] ,[facebook] ,[twitter] ,[visio_esp] ,[visio_por] ,[visio_eng] ,[destacada] ,[pub_o_new] ,[Notificado] ,[fechaNotificacion] ,[thumbnail] ,[subtipopub] ,[visio_chi] ,[is_flipping] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[noticias]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('noticias.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla noticias');
        $this->logger->info('Se ha guardado con el nombre noticias.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
    public function NoticiasIdioma($conn,$output){
        $query = "SELECT  [id] ,[noticias_id] ,[title] ,[summary] ,[contenido] ,[url_pdf] ,[url_link] ,[url_friend] ,[url_video] ,[url_podcast] ,[tags] ,[idiomas_id] ,[pie_foto] ,[url_imgs] ,[url_docs] ,[metadescription] ,[abogado_tags] ,[oficina_tags],[practica_tags] FROM [web_cuatrecasas_cms_desarrollo].[dbo].[noticiasidioma]";  
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile('noticiasIdioma.json', json_encode($results));
        $this->logger->info('Se ha guardado la tabla noticiasIdioma');
        $this->logger->info('Se ha guardado con el nombre noticiasIdioma.json');
        $this->logger->info('Total de registros: '.$stmt->rowCount());
        return 0;
    }
}
<?php

namespace App\Controller\CMS;
use App\Entity\Lawyer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;



class dashboardController extends AbstractController
{

    /**
     * @Route("cms/dashboard", name="dashboard")
     */
    public function index(ContainerInterface $container)
    {
        // $session = new Session();
        // $user = $session->get('User');
        // $data = file_get_contents("abogados.json");
        // $abogados = json_decode($data, true);


        // $em = $container->get('doctrine')->getManager();
        // $conn = $em->getConnection();

        // $conn->executeQuery("DELETE FROM [LawyerTranslation]
        // DBCC CHECKIDENT ('[web_cuatrecasas_cms_Symfony].[dbo].[LawyerTranslation]',RESEED, 0)");
        // $conn->executeQuery("DELETE FROM [Person]
        // DBCC CHECKIDENT ('[web_cuatrecasas_cms_Symfony].[dbo].[Person]',RESEED, 0)");
        // foreach ($abogados as $key => $abogado) {
        //     $nuevoid = $abogado['id_abogado'];
        //     if($key==0){
        //         $viejoid = 1;
        //     }else{
        //         $viejoid = $abogados[$key-1]['id_abogado'];
        //     }
        //     if($abogado['lang'] == "esp"){
        //         $lang = 'es';
        //     }else{
        //         echo(var_dump($abogado['lang']));
        //     }
        //     if($abogado['lang'] == 'eng'){
        //         $lang = 'en';
        //     }else{
        //         echo(var_dump($abogado['lang']));
        //         $lang = $abogado['lang'];
        //     }
        //     dd($lang);
        //     if($nuevoid != $viejoid){
        //         $lawyer = new Lawyer();
        //         $lawyer->setUserId($abogado['siglas']);
        //         $lawyer->setName($abogado['nombre']);
        //         $lawyer->setSurname($abogado['apellidos']);
        //         $lawyer->setEmail($abogado['email']);
        //         $lawyer->setPhone(intval($abogado['telefono']));
        //         $lawyer->setFax(intval($abogado['fax']));
        //         $lawyer->setPhoto($abogado['image']);
        //         $lawyer->setLawyerType($abogado['idtipoabogado']);
        //         $lawyer->setStatus($abogado['status']);
        //     }

        //         $lawyer->translate($lang)->setDescription($abogado['descripcion']);
        //         $lawyer->translate($lang)->setCv($abogado['CV']);
        //         $lawyer->translate($lang)->setExperience($abogado['experiencia']);
        //         $lawyer->translate($lang)->setTags($abogado['tags']);
        //         $lawyer->translate($lang)->setTraining($abogado['formacion']);
        //         $lawyer->translate($lang)->setMentions($abogado['menciones']);
            
        //     if($nuevoid != $viejoid){
        //         $em->persist($lawyer);
        //         $lawyer->mergeNewTranslations();
        //         $em->flush();
        //     }


        // }

       // dd((array)$user);
        return $this->render('cms/dashboard/index.html.twig', [
            'controller_name' => 'dashboardController',
            // 'user' => (array)$user,
        ]);
    }
}

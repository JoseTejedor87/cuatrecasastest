<?php

namespace App\Controller\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Web\WebController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\LawyerRepository;

class ajaxGetLawersController extends WebController
{
/**                                                                                   
 * @Route("/ajax", name="recherche_ajax")
 */
public function ajaxAction(Request $request, LawyerRepository $lawyerRepository)    
{
    // aa;
    $initial = $request->get('initial');
    $page = $request->get('page');
    if(!isset($page))
        $page = 1;  
        $limit = 6;
        if($initial ){
            //$lawyers = $lawyerRepository->findBy(['surname'=> 'p%']); 
            // createQuery("SELECT TOP * FROM Lawyer where surname like 'p%'");
            $query = $lawyerRepository->createQueryBuilder('l')
               ->where('l.surname LIKE :surname')
               ->setParameter('surname', $initial .'%')
               ->setFirstResult($limit * ($page - 1))
               ->setMaxResults($limit)
               ->getQuery();
            $lawyers = $query->getResult();
            $query = $lawyerRepository->createQueryBuilder('l')
               ->where('l.surname LIKE :surname')
               ->setParameter('surname', $initial .'%')
               ->getQuery();
               if($lawyers){
                $countLawyers = count($query->getResult());
                $pagesTotal = $countLawyers/6;
                if(is_float($pagesTotal)){
                    $pagesTotal = $pagesTotal + 1;
                }
               }
            
             //dd($lawyers);
        }
        if($lawyers){
            $lawyerA = array ();
            foreach ($lawyers as $key => $lawyer) {
                $lawyerA[$key] = array( 'FullName' => $lawyer->getName(). ' ' .  $lawyer->getSurname(), 'LawyerType' => $lawyer->getLawyerType(), 'Slug' => $lawyer->getSlug());
            }
        }
    if ($request->isXMLHttpRequest()) {         
        return new JsonResponse(array('lawyers' => $lawyerA,'countLawyers' => $countLawyers,'pagesTotal' => $pagesTotal ,'page' => $page,'initial' => $initial));
    }

    return new Response('This is not ajax!', 400);
}  
}

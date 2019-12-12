<?php

namespace App\Controller\CMS;

use App\Entity\Lawyer;
use App\Form\LawyerType;
use App\Repository\LawyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Route("cms/lawyer")
 */
class LawyerController extends AbstractController
{
    private static $user;
    /**
     * @Route("/", name="lawyer_index", methods={"GET"})
     */
    public function index(LawyerRepository $lawyerRepository): Response
    {
        // $user = LawyerController::session();
        
        return $this->render('cms/lawyer/index.html.twig', [
            'lawyers' => $lawyerRepository->findAll(),
            // 'user' => (array)$user,

        ]);
    }

    /**
     * @Route("/new", name="lawyer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // $user = LawyerController::session();
        $lawyer = new Lawyer();
        $form = $this->createForm(LawyerType::class, $lawyer);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lawyer);
            $lawyer->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('lawyer_index');
         }

        return $this->render('cms/lawyer/new.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
            // 'user' => (array)$user,
        ]);
    }

    /**
     * @Route("/{id}", name="lawyer_show", methods={"GET"})
     */
    public function show(Lawyer $lawyer): Response
    {
        // $user = LawyerController::session();
        return $this->render('cms/lawyer/show.html.twig', [
            'lawyer' => $lawyer,
            // 'user' => (array)$user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lawyer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lawyer $lawyer): Response
    {
        // $user = LawyerController::session();
        $form = $this->createForm(LawyerType::class, $lawyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lawyer_index');
        }

        return $this->render('cms/lawyer/edit.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
            // 'user' => (array)$user,
        ]);
    }

    /**
     * @Route("/{id}", name="lawyer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lawyer $lawyer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lawyer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lawyer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lawyer_index');
    }
    static function session(){
        if(LawyerController::$user == null){
            $session = new Session();
            LawyerController::$user = $session->get('User');
        }
        return LawyerController::$user;
    } 
}

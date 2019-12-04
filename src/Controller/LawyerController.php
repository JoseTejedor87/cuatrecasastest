<?php

namespace App\Controller;

use App\Entity\Lawyer;
use App\Form\LawyerType;
use App\Repository\LawyerRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lawyer")
 */
class LawyerController extends AbstractController
{
    /**
     * @Route("/", name="lawyer_index", methods={"GET"})
     */
    public function index(LawyerRepository $lawyerRepository): Response
    {
        return $this->render('lawyer/index.html.twig', [
            'lawyers' => $lawyerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lawyer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lawyer = new Lawyer();
        $lawyer->translate('fr')->setDescription('Chaussures');
        $lawyer->setUserId('pepe');
        $lawyer->setCreationDate(new \DateTime());
        $lawyer->setUpdateDate(new \DateTime());
        $form = $this->createForm(LawyerType::class, $lawyer);
        //$form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lawyer);
            $lawyer->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('lawyer_index');
         }

        return $this->render('lawyer/new.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lawyer_show", methods={"GET"})
     */
    public function show(Lawyer $lawyer): Response
    {
        return $this->render('lawyer/show.html.twig', [
            'lawyer' => $lawyer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lawyer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lawyer $lawyer): Response
    {
        $form = $this->createForm(LawyerType::class, $lawyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lawyer_index');
        }

        return $this->render('lawyer/edit.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
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
}

<?php

namespace App\Controller\CMS;

use App\Entity\Practice;
use App\Form\PracticeType;
use App\Repository\PracticeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("cms/practice")
 */
class PracticeController extends AbstractController
{
    /**
     * @Route("/", name="practice_index", methods={"GET"})
     */
    public function index(PracticeRepository $practiceRepository): Response
    {
        return $this->render('cms/practice/index.html.twig', [
            'practices' => $practiceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="practice_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $practice = new Practice();
        $form = $this->createForm(PracticeType::class, $practice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($practice);
            $entityManager->flush();

            return $this->redirectToRoute('practice_index');
        }

        return $this->render('cms/practice/new.html.twig', [
            'practice' => $practice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="practice_show", methods={"GET"})
     */
    public function show(Practice $practice): Response
    {
        return $this->render('cms/practice/show.html.twig', [
            'practice' => $practice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="practice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Practice $practice): Response
    {
        $form = $this->createForm(PracticeType::class, $practice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('practice_index');
        }

        return $this->render('cms/practice/edit.html.twig', [
            'practice' => $practice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="practice_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Practice $practice): Response
    {
        if ($this->isCsrfTokenValid('delete'.$practice->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($practice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('practice_index');
    }
}

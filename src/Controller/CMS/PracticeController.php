<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Practice;
use App\Form\PracticeFormType;
use App\Repository\PracticeRepository;
use App\Controller\CMS\CMSController;

/**
 * @Route("cms/practices")
 */
class PracticeController extends CMSController
{
    /**
     * @Route("/", name="practice_index", methods={"GET"})
     */
    public function index(PracticeRepository $practiceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $practiceRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/practice/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="practice_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $practice = new Practice();
        $form = $this->createForm(PracticeFormType::class, $practice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($practice);
            $practice->mergeNewTranslations();
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
        return $this->render('practice/show.html.twig', [
            'practice' => $practice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="practice_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Practice $practice): Response
    {
        $form = $this->createForm(PracticeFormType::class, $practice);
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

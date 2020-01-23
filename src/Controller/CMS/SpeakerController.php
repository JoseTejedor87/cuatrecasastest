<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Speaker;
use App\Form\SpeakerFormType;
use App\Repository\SpeakerRepository;
use App\Controller\CMS\CMSController;

/**
 * @Route("/speakers")
 */
class SpeakerController extends CMSController
{
    /**
     * @Route("/", name="speaker_index", methods={"GET"})
     */
    public function index(SpeakerRepository $speakerRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $speakerRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/speaker/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="speaker_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $speaker = new Speaker();
        $form = $this->createForm(SpeakerFormType::class, $speaker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($speaker);
            $entityManager->flush();

            return $this->redirectToRoute('speaker_index');
        }

        return $this->render('cms/speaker/new.html.twig', [
            'speaker' => $speaker,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="speaker_show", methods={"GET"})
     */
    public function show(Speaker $speaker): Response
    {
        return $this->render('cms/speaker/show.html.twig', [
            'speaker' => $speaker,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="speaker_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Speaker $speaker): Response
    {
        $form = $this->createForm(SpeakerFormType::class, $speaker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('speaker_index');
        }

        return $this->render('cms/speaker/edit.html.twig', [
            'speaker' => $speaker,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="speaker_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Speaker $speaker): Response
    {
        if ($this->isCsrfTokenValid('delete'.$speaker->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($speaker);
            $entityManager->flush();
        }

        return $this->redirectToRoute('speaker_index');
    }
}

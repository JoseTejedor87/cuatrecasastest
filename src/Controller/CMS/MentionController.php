<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Mention;
use App\Form\MentionFormType;
use App\Repository\MentionRepository;
use App\Controller\CMS\CMSController;

/**
 * @Route("cms/mentions")
 */
class MentionController extends CMSController
{
    /**
     * @Route("/", name="mention_index", methods={"GET"})
     */
    public function index(MentionRepository $mentionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $mentionRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/mention/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="mention_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $mention = new Mention();
        $form = $this->createForm(MentionFormType::class, $mention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mention);
            $mention->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('mention_index');
        }

        return $this->render('cms/mention/new.html.twig', [
            'mention' => $mention,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mention_show", methods={"GET"})
     */
    public function show(Mention $mention): Response
    {
        return $this->render('mention/show.html.twig', [
            'mention' => $mention,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="mention_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Mention $mention): Response
    {
        $form = $this->createForm(MentionFormType::class, $mention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mention_index');
        }

        return $this->render('cms/mention/edit.html.twig', [
            'mention' => $mention,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="mention_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Mention $mention): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mention->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($mention);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mention_index');
    }
}

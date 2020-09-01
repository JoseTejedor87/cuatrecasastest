<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Publication;
use App\Entity\Article;
use App\Entity\Opinion;
use App\Entity\LegalNovelty;
use App\Entity\News;
use App\Form\PublicationFormType;
use App\Repository\PublicationRepository;
use App\Controller\CMS\CMSController;

class PublicationController extends CMSController
{
    public function index(PublicationRepository $PublicationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $PublicationRepository->findBy(array(), array('id' => 'DESC')),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/publication/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $type = $request->query->get('type');
        switch ($type) {
            case 'article':
                $Publication = new Article();
                break;
            case 'opinion':
                $Publication = new Opinion();
                break;
            case 'legalNovelty':
                $Publication = new LegalNovelty();
                break;
            case 'news':
                $Publication = new News();
                break;
        }
        
        $form = $this->createForm(PublicationFormType::class, $Publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Publication);
            $Publication->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_publication_index');
        }

        return $this->render('cms/publication/new.html.twig', [
            'Publication' => $Publication,
            'form' => $form->createView(),
        ]);
    }

    public function show(Publication $Publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'Publication' => $Publication,
        ]);
    }

    public function edit(Request $request, Publication $Publication): Response
    {
        $form = $this->createForm(PublicationFormType::class, $Publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_publication_edit', ['id'=>$Publication->getId()]);
        }

        return $this->render('cms/publication/edit.html.twig', [
            'Publication' => $Publication,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Publication $Publication): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Publication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_publication_index');
    }
}
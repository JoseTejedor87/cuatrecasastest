<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Page;
use App\Form\PageFormType;
use App\Repository\PageRepository;
use App\Controller\CMS\CMSController;

class PageController extends CMSController
{
    public function index(PageRepository $pageRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $pageRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/page/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($page);
            $page->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_pages_index');
        }

        return $this->render('cms/page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    public function show(Page $page): Response
    {
        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }

    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_pages_edit', ['id'=>$page->getId()]);
        }

        return $this->render('cms/page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($page);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_pages_index');
    }
}

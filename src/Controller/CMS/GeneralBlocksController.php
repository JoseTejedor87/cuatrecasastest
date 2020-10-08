<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\GeneralBlock;
use App\Form\GeneralBlockFormType;
use App\Repository\GeneralBlockRepository;
use App\Controller\CMS\CMSController;

class GeneralBlocksController extends CMSController
{
    public function index(GeneralBlockRepository $generalBlockRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $generalBlockRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/block/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $block = new GeneralBlock();
        $form = $this->createForm(GeneralBlockFormType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($block);
            $block->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_blocks_index');
        }

        return $this->render('cms/block/new.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    public function show(GeneralBlock $block): Response
    {
        return $this->render('block/show.html.twig', [
            'block' => $block,
        ]);
    }

    public function edit(Request $request, GeneralBlock $block): Response
    {
        $form = $this->createForm(GeneralBlockFormType::class, $block);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_blocks_edit', ['id'=>$block->getId()]);
        }

        return $this->render('cms/block/edit.html.twig', [
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, GeneralBlock $block): Response
    {
        if ($this->isCsrfTokenValid('delete'.$block->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($block);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_blocks_index');
    }
}

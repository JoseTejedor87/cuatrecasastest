<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Desk;
use App\Form\DeskFormType;
use App\Repository\DeskRepository;
use App\Controller\CMS\CMSController;

class DeskController extends CMSController
{
    public function index(DeskRepository $deskRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $deskRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/desk/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $desk = new Desk();
        $form = $this->createForm(DeskFormType::class, $desk);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($desk);
            $desk->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_desks_index');
        }

        return $this->render('cms/desk/new.html.twig', [
            'desk' => $desk,
            'form' => $form->createView(),
        ]);
    }

    public function show(Desk $desk): Response
    {
        return $this->render('desk/show.html.twig', [
            'desk' => $desk,
        ]);
    }

    public function edit(Request $request, Desk $desk): Response
    {
        $form = $this->createForm(DeskFormType::class, $desk);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_desks_index');
        }

        return $this->render('cms/desk/edit.html.twig', [
            'desk' => $desk,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Desk $desk): Response
    {
        if ($this->isCsrfTokenValid('delete'.$desk->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($desk);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_desks_index');
    }
}

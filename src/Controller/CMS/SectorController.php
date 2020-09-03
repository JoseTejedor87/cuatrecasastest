<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Sector;
use App\Form\SectorFormType;
use App\Repository\SectorRepository;
use App\Controller\CMS\CMSController;

class SectorController extends CMSController
{
    public function index(SectorRepository $sectorRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $sectorRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/sector/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $sector = new Sector();
        $form = $this->createForm(SectorFormType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sector);
            $sector->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_sectors_index');
        }

        return $this->render('cms/sector/new.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    public function show(Sector $sector): Response
    {
        return $this->render('sector/show.html.twig', [
            'sector' => $sector,
        ]);
    }

    public function edit(Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorFormType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_sectors_edit', ['id'=>$sector->getId()]);
        }

        return $this->render('cms/sector/edit.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Sector $sector): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sector->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sector);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_sectors_index');
    }
}

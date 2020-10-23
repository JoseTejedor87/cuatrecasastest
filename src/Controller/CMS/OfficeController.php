<?php

namespace App\Controller\CMS;

use App\Entity\Office;
use App\Form\OfficeFormType;
use App\Repository\OfficeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\CMS\CMSController;

class OfficeController extends CMSController
{
    public function index(OfficeRepository $officeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $officeRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/office/index.html.twig', [
            'offices' => $officeRepository->findAll(),
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $office = new Office();
        $form = $this->createForm(OfficeFormType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($office);
            $office->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_offices_index');
        }

        return $this->render('cms/office/new.html.twig', [
            'office' => $office,
            'form' => $form->createView(),
        ]);
    }

    public function show(Office $office): Response
    {
        return $this->render('cms/office/show.html.twig', [
            'office' => $office,
        ]);
    }

    public function edit(Request $request, Office $office): Response
    {
        $form = $this->createForm(OfficeFormType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('office_form')['img_office'])) {
                $photo = $request->request->get('office_form')['img_office'];
                if (isset($photo['file']['delete']) && $photo['file']['delete'] == "1") {
                    $office->setImgOffice(null);
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_offices_index');
        }

        return $this->render('cms/office/edit.html.twig', [
            'office' => $office,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Office $office): Response
    {
        if ($this->isCsrfTokenValid('delete'.$office->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($office);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_offices_index');
    }
}

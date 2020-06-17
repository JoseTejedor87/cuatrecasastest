<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\CaseStudy;
use App\Form\CaseStudyFormType;
use App\Repository\CaseStudyRepository;
use App\Controller\CMS\CMSController;

class CaseStudyController extends CMSController
{
    public function index(CaseStudyRepository $caseStudyRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $caseStudyRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/casestudy/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $caseStudy = new CaseStudy();
        $form = $this->createForm(CaseStudyFormType::class, $caseStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($caseStudy);
            $caseStudy->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_case_studies_index');
        }

        return $this->render('cms/casestudy/new.html.twig', [
            'casestudy' => $caseStudy,
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, CaseStudy $caseStudy): Response
    {
        $form = $this->createForm(CaseStudyFormType::class, $caseStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_case_studies_edit', ['id'=>$caseStudy->getId()]);
        }

        return $this->render('cms/casestudy/edit.html.twig', [
            'casestudy' => $caseStudy,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, CaseStudy $caseStudy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$caseStudy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($caseStudy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_case_studies_index');
    }
}

<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Insight;
use App\Form\InsightFormType;
use App\Repository\InsightRepository;
use App\Controller\CMS\CMSController;

class InsightController extends CMSController
{
    public function index(InsightRepository $insightRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $insightRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/insight/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $insight = new Insight();
        $form = $this->createForm(InsightFormType::class, $insight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($insight);
            $insight->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_insights_index');
        }

        return $this->render('cms/insight/new.html.twig', [
            'insight' => $insight,
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, Insight $insight): Response
    {
        $form = $this->createForm(InsightFormType::class, $insight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_insights_edit', ['id'=>$insight->getId()]);
        }

        return $this->render('cms/insight/edit.html.twig', [
            'insight' => $insight,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Insight $insight): Response
    {
        if ($this->isCsrfTokenValid('delete'.$insight->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($insight);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_insight_index');
    }
}

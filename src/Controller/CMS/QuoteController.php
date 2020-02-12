<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Quote;
use App\Form\QuoteFormType;
use App\Repository\QuoteRepository;
use App\Controller\CMS\CMSController;

/**
 * @Route("/quotes")
 */
class QuoteController extends CMSController
{
    /**
     * @Route("/", name="quote_index", methods={"GET"})
     */
    public function index(QuoteRepository $quoteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $quoteRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/quote/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="quote_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteFormType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('quote_index');
        }

        return $this->render('cms/quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quote_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quote $quote): Response
    {
        $form = $this->createForm(QuoteFormType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quote_index');
        }

        return $this->render('cms/quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quote_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quote $quote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quote_index');
    }
}

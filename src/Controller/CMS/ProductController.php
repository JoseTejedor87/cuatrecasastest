<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Controller\CMS\CMSController;

class ProductController extends CMSController
{
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $productRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/product/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $product->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_products_index');
        }

        return $this->render('cms/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_products_edit', ['id'=>$product->getId()]);
        }

        return $this->render('cms/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_products_index');
    }
}

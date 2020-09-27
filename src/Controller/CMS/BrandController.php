<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\BrandCategoryType;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Brand;
use App\Form\BrandFormType;
use App\Repository\BrandRepository;
use App\Controller\CMS\CMSController;

class BrandController extends CMSController
{
    public function index(BrandRepository $brandRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $result = $brandRepository->findAll();
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/brand/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandFormType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $brand->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_brands_index');
        }

        return $this->render('cms/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    public function show(Brand $brand): Response
    {
        return $this->render('cms/brand/show.html.twig', [
            'brand' => $brand
        ]);
    }

    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->createForm(BrandFormType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (isset($request->request->get('brand_form')['image'])) {
                $attachments = $request->request->get('brand_form')['image'];
                foreach ($attachments as $key => $attachment) {
                    if (isset($attachment['file']['delete']) && $attachment['file']['delete'] == "1") {
                        $brand->removeAttachment(
                            $brand->getAttachments()[$key]
                        );
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_brands_edit', ['id'=>$brand->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Brand $brand): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($brand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_brands_index');
    }
}

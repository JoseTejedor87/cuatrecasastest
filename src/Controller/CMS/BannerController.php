<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\BannerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Banner;
use App\Form\BannerFormType;
use App\Repository\BannerRepository;
use App\Controller\CMS\CMSController;

class BannerController extends CMSController
{
    public function index(BannerRepository $bannerRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $result = $bannerRepository->findAll();
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/banner/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $banner = new Banner();
        $form = $this->createForm(BannerFormType::class, $banner);

        //dd($banner); die();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($banner);
            $entityManager->flush();

            return $this->redirectToRoute('cms_banners_index');
        }

        return $this->render('cms/banner/new.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }

    public function show(Banner $banner): Response
    {
        return $this->render('cms/banner/show.html.twig', [
            'banner' => $banner
        ]);
    }

    public function edit(Request $request, Banner $banner): Response
    {
         //dd($banner); die();
        $form = $this->createForm(BannerFormType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_banners_edit', ['id'=>$banner->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/banner/edit.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Banner $banner): Response
    {
        if ($this->isCsrfTokenValid('delete'.$banner->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($banner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_banners_index');
    }
}

<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\HomeCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Home;
use App\Form\HomeFormType;
use App\Repository\HomeRepository;
use App\Controller\CMS\CMSController;

class HomeController extends CMSController
{
    public function index(HomeRepository $HomeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $result = $HomeRepository->findAll();
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $home = new Home();
        $form = $this->createForm(HomeFormType::class, $home);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($home);
            $entityManager->flush();

            return $this->redirectToRoute('cmd_home_new');
        }

        return $this->render('cms/home/new.html.twig', [
            'home' => $home,
            'form' => $form->createView(),
        ]);
    }

    public function show(Home $Home): Response
    {
        return $this->render('cms/home/show.html.twig', [
            'home' => $home
        ]);
    }

    public function edit(Request $request, Home $home): Response
    {
         
        $form = $this->createForm(HomeFormType::class, $home);
        $form->handleRequest($request);

        //dd($form);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_home_edit', ['id'=>$home->getId()]);
        }

        //dd($form);

        return $this->render('cms/home/edit.html.twig', [
            'home' => $home,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Home $Home): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Home->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Home);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_home_index');
    }
}

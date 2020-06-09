<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Lawyer;
use App\Form\LawyerFormType;
use App\Repository\LawyerRepository;
use App\Controller\CMS\CMSController;

class LawyerController extends CMSController
{
    public function index(LawyerRepository $lawyerRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $lawyerRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/lawyer/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $lawyer = new Lawyer();
        $form = $this->createForm(LawyerFormType::class, $lawyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lawyer);
            $lawyer->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_lawyers_index');
        }

        return $this->render('cms/lawyer/new.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
        ]);
    }

    public function show(Lawyer $lawyer): Response
    {
        return $this->render('cms/lawyer/show.html.twig', [
            'lawyer' => $lawyer
        ]);
    }

    public function edit(Request $request, Lawyer $lawyer): Response
    {
        $form = $this->createForm(LawyerFormType::class, $lawyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('lawyer_form')['photo'])) {
                $photo = $request->request->get('lawyer_form')['photo'];
                if (isset($photo['file']['delete']) && $photo['file']['delete'] == "1") {
                    $lawyer->setPhoto(null);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_lawyers_edit', ['id'=>$lawyer->getId()]);
        }

        return $this->render('cms/lawyer/edit.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Lawyer $lawyer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lawyer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lawyer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_lawyers_index');
    }
}

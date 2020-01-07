<?php

namespace App\Controller\CMS;

use App\Entity\Lawyer;
use App\Form\LawyerType;
use App\Repository\LawyerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Route("cms/lawyer")
 */
class LawyerController extends AbstractController
{

    /**
     * @Route("/", name="lawyer_index", methods={"GET"})
     */
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

    /**
     * @Route("/new", name="lawyer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $lawyer = new Lawyer();
        $form = $this->createForm(LawyerType::class, $lawyer);
        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lawyer);
            $lawyer->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('lawyer_index');
         }

        return $this->render('cms/lawyer/new.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lawyer_show", methods={"GET"})
     */
    public function show(Lawyer $lawyer): Response
    {
        return $this->render('cms/lawyer/show.html.twig', [
            'lawyer' => $lawyer
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lawyer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lawyer $lawyer): Response
    {
        $form = $this->createForm(LawyerType::class, $lawyer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lawyer_index');
        }

        return $this->render('cms/lawyer/edit.html.twig', [
            'lawyer' => $lawyer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lawyer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lawyer $lawyer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lawyer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lawyer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lawyer_index');
    }
}

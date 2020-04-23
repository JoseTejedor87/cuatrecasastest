<?php

namespace App\Controller\CMS;

use App\Entity\Award;
use App\Form\AwardFormType;
use App\Repository\AwardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\CMS\CMSController;

/**
 * @Route("cms/awards")
 */
class AwardController extends CMSController
{
    /**
     * @Route("/", name="awards_index", methods={"GET"})
     */
    public function index(AwardRepository $awardRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $awardRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/award/index.html.twig', [
            'awards' => $awardRepository->findAll(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="awards_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $award = new Award();
        $form = $this->createForm(AwardFormType::class, $award);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($award);
            $award->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('awards_index');
        }

        return $this->render('cms/award/new.html.twig', [
            'award' => $award,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="awards_show", methods={"GET"})
     */
    public function show(Award $award): Response
    {
        return $this->render('cms/award/show.html.twig', [
            'award' => $award,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="awards_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Award $award): Response
    {
        $form = $this->createForm(AwardFormType::class, $award);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('award_form')['img_office'])) {
                $photo = $request->request->get('award_form')['img_office'];
                if (isset($photo['file']['delete']) && $photo['file']['delete'] == "1") {
                    $award->setImgOffice(null);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('awards_index');
        }

        return $this->render('cms/award/edit.html.twig', [
            'award' => $award,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="awards_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Award $award): Response
    {
        if ($this->isCsrfTokenValid('delete'.$award->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($award);
            $entityManager->flush();
        }

        return $this->redirectToRoute('awards_index');
    }
}

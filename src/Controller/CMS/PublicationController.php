<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Knp\Component\Pager\PaginatorInterface;

use App\Form\Type\PublicationCategoryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Publication;
use App\Entity\Opinion;
use App\Entity\LegalNovelty;
use App\Entity\Academy;
use App\Entity\News;
use App\Form\PublicationFormType;
use App\Repository\PublicationRepository;
use App\Repository\LegislationRepository;
use App\Controller\CMS\CMSController;

class PublicationController extends CMSController
{
    public function index(PublicationRepository $PublicationRepository, LegislationRepository $legislationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filter = $this->filter($request, $legislationRepository);

        if ($filter['fields'] != '') {
            $result = $PublicationRepository->findFilteredBy($filter['fields']);
        } else {
            $result = $PublicationRepository->findBy(array(), array('id' => 'DESC'));
        }
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );
        foreach ($pagination as $key => $value) {
            if ($value instanceof \App\Entity\LegalNovelty) {
                $value->type = 'legalNovelty';
            }
            if ($value instanceof \App\Entity\Academy) {
                $value->type = 'academy';
            }
            if ($value instanceof \App\Entity\Opinion) {
                $value->type = 'opinion';
            }
            if ($value instanceof \App\Entity\News) {
                $value->type = 'news';
            }
        }
        return $this->render('cms/publication/index.html.twig', [
            'pagination' => $pagination,
            'formForFilterView' => $filter['form']->createView(),
        ]);
    }

    private function filter(Request $request, LegislationRepository $legislationRepository)
    {
        $legislationArray = array('Seleccionar' => 0);
        $legislations = $legislationRepository->findAll();
        foreach ($legislations as $legislation) {
            $legislationArray[$legislation->getName()] = $legislation->getId();
        }
        $formForFilter = $this->createFormBuilder(array(), [ 'translation_domain' => 'admin'])
            ->setMethod('GET')
            ->add('title', TextType::class, ['required' => false, 'label' => false ])
            ->add('type', PublicationCategoryType::class, ['required' => false,'label'=> false ])
            ->add('fechaDesde', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('fechaHasta', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('legislation', ChoiceType::class, ['label'=>false,'choices'  => $legislationArray])
            ->add('send', SubmitType::class, ['label'=> 'Filtrar' ])
            ->getForm();

        $formForFilter->handleRequest($request);
        $filterFields = '';

        if ($formForFilter->isSubmitted() && $formForFilter->isValid()) {
            $filterFields = $formForFilter->getData();
        }

        return array('form' => $formForFilter, 'fields' => $filterFields);
    }


    public function new(Request $request): Response
    {
        $type = $request->query->get('type');
        switch ($type) {
            case 'academy':
                $Publication = new Academy();
                break;
            case 'opinion':
                $Publication = new Opinion();
                break;
            case 'legalNovelty':
                $Publication = new LegalNovelty();
                break;
            case 'news':
                $Publication = new News();
                break;
        }

        $form = $this->createForm(PublicationFormType::class, $Publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Publication);
            $Publication->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_publication_index');
        }

        return $this->render('cms/publication/new.html.twig', [
            'Publication' => $Publication,
            'form' => $form->createView(),
        ]);
    }

    public function show(Publication $Publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'Publication' => $Publication,
        ]);
    }

    public function edit(Request $request, Publication $Publication): Response
    {
        $form = $this->createForm(PublicationFormType::class, $Publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_publication_edit', ['id'=>$Publication->getId()]);
        }

        return $this->render('cms/publication/edit.html.twig', [
            'Publication' => $Publication,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Publication $Publication): Response
    {
        if ($this->isCsrfTokenValid('delete'.$Publication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($Publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_publication_index');
    }
}

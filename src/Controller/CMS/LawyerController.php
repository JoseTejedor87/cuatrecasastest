<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Lawyer;
use App\Form\LawyerFormType;
use App\Repository\LawyerRepository;
use App\Controller\CMS\CMSController;
use App\Form\Type\LawyerCategoryType;

class LawyerController extends CMSController
{
    public function index(LawyerRepository $lawyerRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filter = $this->filter($request);

        if ( $filter['fields'] != ''){
            $result = $lawyerRepository->findFilteredBy($filter['fields']);
        }else{
            $result = $lawyerRepository->findAll();
        }

        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/lawyer/index.html.twig', [
            'pagination' => $pagination,
            'formForFilterView' => $filter['form']->createView(),
        ]);
    }

    private function filter(Request $request){
        $formForFilter = $this->createFormBuilder(array())
            ->setMethod('GET')
            ->add('name', TextType::class, ['required' => false, 'label' => false ])
            ->add('surname', TextType::class, ['required' => false, 'label' => false ])
            ->add('email', TextType::class, ['required' => false, 'label' => false])
            ->add('lawyerType', LawyerCategoryType::class, ['required' => false,'label'=> false ])
            ->add('fechaDesde', DateType::class, ['label'=>'Desde', 'widget' => 'single_text', 'required' => false])
            ->add('fechaHasta', DateType::class, ['label'=>'Hasta', 'widget' => 'single_text', 'required' => false])
            ->add('send', SubmitType::class)
            ->getForm();
    
        $formForFilter->handleRequest($request);
        $filterFields = '';

        if ($formForFilter->isSubmitted() && $formForFilter->isValid()) {
            // filterFields is an array with "name", "email" keys
            $filterFields = $formForFilter->getData();
        }

        return array('form' => $formForFilter, 'fields' => $filterFields);
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

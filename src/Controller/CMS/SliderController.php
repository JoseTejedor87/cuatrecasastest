<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\SliderCategoryType;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Slider;
use App\Form\SliderFormType;
use App\Repository\SliderRepository;
use App\Controller\CMS\CMSController;

class SliderController extends CMSController
{
    public function index(SliderRepository $sliderRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $result = $sliderRepository->findAll();
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/slider/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $slider = new Slider();
        $form = $this->createForm(SliderFormType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slider);
            $slider->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_sliders_index');
        }

        return $this->render('cms/slider/new.html.twig', [
            'slider' => $slider,
            'form' => $form->createView(),
        ]);
    }

    public function show(Slider $slider): Response
    {
        return $this->render('cms/slider/show.html.twig', [
            'slider' => $slider
        ]);
    }

    public function edit(Request $request, Slider $slider): Response
    {
        $form = $this->createForm(SliderFormType::class, $slider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('slider_form')['attachments'])) {
                $attachments = $request->request->get('slider_form')['attachments'];
                foreach ($attachments as $key => $attachment) {
                    if (isset($attachment['file']['delete']) && $attachment['file']['delete'] == "1") {
                        $slider->removeAttachment(
                            $slider->getAttachments()[$key]
                        );
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_sliders_edit', ['id'=>$slider->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/slider/edit.html.twig', [
            'Slider' => $slider,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Slider $slider): Response
    {
        if ($this->isCsrfTokenValid('delete'.$slider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($slider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_sliders_index');
    }
}

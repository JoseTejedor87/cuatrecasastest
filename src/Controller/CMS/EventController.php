<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Form\Type\EventCategoryType;
use App\Form\Type\LawyerCategoryType;
use App\Form\Type\LanguageType;
use App\Form\Type\RegionType;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Controller\CMS\CMSController;

class EventController extends CMSController
{
    public function index(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $filter = $this->filter($request);

        if ( $filter['fields'] != ''){
            $result = $eventRepository->findFilteredBy($filter['fields']);
        }else{
            $result = $eventRepository->findAll();
        }
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/event/index.html.twig', [
            'pagination' => $pagination,
            'formForFilterView' => $filter['form']->createView(),
        ]);
    }

    private function filter(Request $request){
        $formForFilter = $this->createFormBuilder(array(),[ 'translation_domain' => 'admin'])
            ->setMethod('GET')
            ->add('title', TextType::class, ['required' => false, 'label' => false ])
            ->add('eventType', EventCategoryType::class, ['required' => false,'label'=> false ])
            ->add('inicioDesde', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('inicioHasta', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('finDesde', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('finHasta', DateType::class, ['label'=>false, 'widget' => 'single_text', 'required' => false])
            ->add('languages', LanguageType::class, ['label'=>false])
            ->add('regions', RegionType::class, ['label'=>false])
            ->add('send', SubmitType::class,['label'=> 'Filtrar' ])
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
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $event->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_events_index');
        }

        return $this->render('cms/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function show(Event $event): Response
    {
        return $this->render('cms/event/show.html.twig', [
            'event' => $event
        ]);
    }

    public function edit(Request $request, Event $event): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('event_form')['attachments'])) {
                $attachments = $request->request->get('event_form')['attachments'];
                foreach ($attachments as $key => $attachment) {
                    if (isset($attachment['file']['delete']) && $attachment['file']['delete'] == "1") {
                        $event->removeAttachment(
                            $event->getAttachments()[$key]
                        );
                    }
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_events_edit', ['id'=>$event->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_events_index');
    }
}

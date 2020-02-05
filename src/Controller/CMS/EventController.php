<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use App\Controller\CMS\CMSController;

/**
 * @Route("cms/events")
 */
class EventController extends CMSController
{

    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $eventRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/event/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET","POST"})
     */
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

            return $this->redirectToRoute('event_index');
         }

        return $this->render('cms/event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('cms/event/show.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET","POST"})
     */
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

            return $this->redirectToRoute('event_edit', ['id'=>$event->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }
}

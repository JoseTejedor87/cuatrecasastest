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

use App\Entity\Program;
use App\Form\ProgramFormType;
use App\Repository\ProgramRepository;
use App\Repository\EventRepository;
use App\Controller\CMS\CMSController;

class ProgramController extends CMSController
{
    public function index(ProgramRepository $programRepository, PaginatorInterface $paginator, Request $request): Response
    {   

        $result= $programRepository->findBy(array("events" => $request->attributes->get('id') ));
 
        $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('cms/program/index.html.twig', [
            'pagination'    => $pagination,
            'event_id'      => $request->attributes->get('id')
        ]);
    }

    public function new(EventRepository $eventRepository,Request $request): Response
    {
        $program = new program();

        $event = $eventRepository->find($request->attributes->get('id'));

        $form = $this->createForm(ProgramFormType::class, $program);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        
            $program->setEvents($event);        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($program);
            $program->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_programs_index', array('id' => $request->attributes->get('id')));
        }

        return $this->render('cms/program/new.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
            'event_id'      => $request->attributes->get('id')
        ]);
    }

    public function edit(Request $request, Program $program): Response
    {
        $form = $this->createForm(ProgramFormType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_programs_edit', ['id'=>$program->getId()]);
        }

        $a = $form->createView();

        return $this->render('cms/program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
            'event_id' => $program->getEvents()->getId()
        ]);
    }

    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_programs_index');
    }
}

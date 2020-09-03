<?php

namespace App\Controller\CMS;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Person;
use App\Form\PersonFormType;
use App\Repository\PersonRepository;
use App\Controller\CMS\CMSController;

class PersonController extends CMSController
{
    public function index(PersonRepository $personRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $personRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/person/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function new(Request $request): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($person);
            $entityManager->flush();

            return $this->redirectToRoute('cms_people_index');
        }

        return $this->render('cms/person/new.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    public function show(Person $person): Response
    {
        return $this->render('cms/person/show.html.twig', [
            'person' => $person,
        ]);
    }

    public function edit(Request $request, Person $person): Response
    {
        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_people_index');
        }

        return $this->render('cms/person/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Person $person): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($person);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_people_index');
    }
}

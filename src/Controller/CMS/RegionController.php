<?php


namespace App\Controller\CMS;


use App\Entity\Region;
use App\Form\RegionFormType;
use App\Repository\RegionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends CMSController
{
    public function index(RegionRepository $regionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $regionRepository->findAll(),
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('cms/region/index.html.twig', [
            'region' => $regionRepository->findAll(),
            'pagination' => $pagination
        ]);
    }

    public function new(Request $request): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionFormType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $region->mergeNewTranslations();
            $entityManager->flush();

            return $this->redirectToRoute('cms_region_index');
        }

        return $this->render('cms/region/new.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, Region $region): Response
    {
        $form = $this->createForm(RegionFormType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('region_form')['photo'])) {
                $photo = $request->request->get('region_form')['photo'];
                if (isset($photo['file']['delete']) && $photo['file']['delete'] == "1") {
                    $region->setPhoto(null);
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cms_region_index');
        }

        return $this->render('cms/region/edit.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    public function delete(Request $request, Region $region): Response
    {
        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($region);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_region_index');
    }
}
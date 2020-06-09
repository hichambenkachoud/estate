<?php

namespace App\Controller\Admin;

use App\Entity\AdvertType;
use App\Form\AdvertTypeType;
use App\Repository\AdvertTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advert/type")
 */
class AdvertTypeController extends AbstractController
{
    /**
     * @Route("/", name="advert_type_index", methods={"GET"})
     */
    public function index(AdvertTypeRepository $advertTypeRepository): Response
    {
        return $this->render('backend/advert_type/index.html.twig', [
            'advert_types' => $advertTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="advert_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $advertType = new AdvertType();
        $form = $this->createForm(AdvertTypeType::class, $advertType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($advertType);
            $entityManager->flush();

            return $this->redirectToRoute('advert_type_index');
        }

        return $this->render('backend/advert_type/new.html.twig', [
            'advert_type' => $advertType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_type_show", methods={"GET"})
     */
    public function show(AdvertType $advertType): Response
    {
        return $this->render('backend/advert_type/show.html.twig', [
            'advert_type' => $advertType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="advert_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AdvertType $advertType): Response
    {
        $form = $this->createForm(AdvertTypeType::class, $advertType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_type_index');
        }

        return $this->render('backend/advert_type/edit.html.twig', [
            'advert_type' => $advertType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, AdvertType $advertType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advertType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advertType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('advert_type_index');
    }
}

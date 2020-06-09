<?php

namespace App\Controller\Admin;

use App\Entity\EstateType;
use App\Form\EstateTypeType;
use App\Repository\EstateTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estate/type")
 */
class EstateTypeController extends AbstractController
{
    /**
     * @Route("/", name="estate_type_index", methods={"GET"})
     */
    public function index(EstateTypeRepository $estateTypeRepository): Response
    {
        return $this->render('backend/estate_type/index.html.twig', [
            'estate_types' => $estateTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="estate_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $estateType = new EstateType();
        $form = $this->createForm(EstateTypeType::class, $estateType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($estateType);
            $entityManager->flush();

            return $this->redirectToRoute('estate_type_index');
        }

        return $this->render('backend/estate_type/new.html.twig', [
            'estate_type' => $estateType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_type_show", methods={"GET"})
     */
    public function show(EstateType $estateType): Response
    {
        return $this->render('backend/estate_type/show.html.twig', [
            'estate_type' => $estateType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estate_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EstateType $estateType): Response
    {
        $form = $this->createForm(EstateTypeType::class, $estateType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estate_type_index');
        }

        return $this->render('backend/estate_type/edit.html.twig', [
            'estate_type' => $estateType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EstateType $estateType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estateType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($estateType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('estate_type_index');
    }
}

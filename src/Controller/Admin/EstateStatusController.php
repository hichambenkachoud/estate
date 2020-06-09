<?php

namespace App\Controller\Admin;

use App\Entity\EstateStatus;
use App\Form\EstateStatusType;
use App\Repository\EstateStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estate/status")
 */
class EstateStatusController extends AbstractController
{
    /**
     * @Route("/", name="estate_status_index", methods={"GET"})
     */
    public function index(EstateStatusRepository $estateStatusRepository): Response
    {
        return $this->render('backend/estate_status/index.html.twig', [
            'estate_statuses' => $estateStatusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="estate_status_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $estateStatus = new EstateStatus();
        $form = $this->createForm(EstateStatusType::class, $estateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($estateStatus);
            $entityManager->flush();

            return $this->redirectToRoute('estate_status_index');
        }

        return $this->render('backend/estate_status/new.html.twig', [
            'estate_status' => $estateStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_status_show", methods={"GET"})
     */
    public function show(EstateStatus $estateStatus): Response
    {
        return $this->render('backend/estate_status/show.html.twig', [
            'estate_status' => $estateStatus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estate_status_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EstateStatus $estateStatus): Response
    {
        $form = $this->createForm(EstateStatusType::class, $estateStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estate_status_index');
        }

        return $this->render('backend/estate_status/edit.html.twig', [
            'estate_status' => $estateStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EstateStatus $estateStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estateStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($estateStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('estate_status_index');
    }
}

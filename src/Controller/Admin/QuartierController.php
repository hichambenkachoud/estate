<?php

namespace App\Controller\Admin;

use App\Entity\Quartier;
use App\Form\QuartierType;
use App\Repository\QuartierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quartier")
 */
class QuartierController extends AbstractController
{
    /**
     * @Route("/", name="quartier_index", methods={"GET"})
     */
    public function index(QuartierRepository $quartierRepository): Response
    {
        return $this->render('backend/quartier/index.html.twig', [
            'quartiers' => $quartierRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quartier_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quartier = new Quartier();
        $form = $this->createForm(QuartierType::class, $quartier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quartier);
            $entityManager->flush();

            return $this->redirectToRoute('quartier_index');
        }

        return $this->render('backend/quartier/new.html.twig', [
            'quartier' => $quartier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quartier_show", methods={"GET"})
     */
    public function show(Quartier $quartier): Response
    {
        return $this->render('backend/quartier/show.html.twig', [
            'quartier' => $quartier,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quartier_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quartier $quartier): Response
    {
        $form = $this->createForm(QuartierType::class, $quartier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quartier_index');
        }

        return $this->render('backend/quartier/edit.html.twig', [
            'quartier' => $quartier,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quartier_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quartier $quartier): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quartier->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quartier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quartier_index');
    }
}

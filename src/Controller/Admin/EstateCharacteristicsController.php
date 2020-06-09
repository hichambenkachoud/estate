<?php

namespace App\Controller\Admin;

use App\Entity\EstateCharacteristics;
use App\Form\EstateCharacteristicsType;
use App\Repository\EstateCharacteristicsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estate/characteristics")
 */
class EstateCharacteristicsController extends AbstractController
{
    /**
     * @Route("/", name="estate_characteristics_index", methods={"GET"})
     */
    public function index(EstateCharacteristicsRepository $estateCharacteristicsRepository): Response
    {
        return $this->render('backend/estate_characteristics/index.html.twig', [
            'estate_characteristics' => $estateCharacteristicsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="estate_characteristics_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $estateCharacteristic = new EstateCharacteristics();
        $form = $this->createForm(EstateCharacteristicsType::class, $estateCharacteristic);
        $form->handleRequest($request);

        if (isset($_POST['estate_characteristics']) && isset($_POST['estate_characteristics']['icon'])){
            $estateCharacteristic->setIcon($_POST['estate_characteristics']['icon']);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($estateCharacteristic);
                $entityManager->flush();

                return $this->redirectToRoute('estate_characteristics_index');
            }

        }

        return $this->render('backend/estate_characteristics/new.html.twig', [
            'estate_characteristic' => $estateCharacteristic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_characteristics_show", methods={"GET"})
     */
    public function show(EstateCharacteristics $estateCharacteristic): Response
    {
        return $this->render('backend/estate_characteristics/show.html.twig', [
            'estate_characteristic' => $estateCharacteristic,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estate_characteristics_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EstateCharacteristics $estateCharacteristic): Response
    {
        $form = $this->createForm(EstateCharacteristicsType::class, $estateCharacteristic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estate_characteristics_index');
        }

        return $this->render('backend/estate_characteristics/edit.html.twig', [
            'estate_characteristic' => $estateCharacteristic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estate_characteristics_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EstateCharacteristics $estateCharacteristic): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estateCharacteristic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($estateCharacteristic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('estate_characteristics_index');
    }
}

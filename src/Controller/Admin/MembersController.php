<?php

namespace App\Controller\Admin;

use App\Entity\Members;
use App\Form\MembersType;
use App\Repository\MembersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members")
 */
class MembersController extends AbstractController
{
    /**
     * @param MembersRepository $membersRepository
     * @Route("/", name="members_index", methods={"GET"})
     * @return Response
     */
    public function index(MembersRepository $membersRepository): Response
    {
        return $this->render('backend/members/index.html.twig', [
            'members' => $membersRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/new", name="members_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $member = new Members();
        $form = $this->createForm(MembersType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('members_index');
        }

        return $this->render('backend/members/new.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="members_show", methods={"GET"})
     */
    public function show(Members $member): Response
    {
        return $this->render('backend/members/show.html.twig', [
            'member' => $member,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="members_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Members $member): Response
    {
        $form = $this->createForm(MembersType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('members_index');
        }

        return $this->render('backend/members/edit.html.twig', [
            'member' => $member,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="members_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Members $member): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('members_index');
    }
}

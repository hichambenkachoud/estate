<?php

namespace App\Controller\Admin;

use App\Entity\Agency;
use App\Form\AgencyType;
use App\Repository\AgencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/agency")
 */
class AgencyController extends AbstractController
{
    private $trans;

    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    /**
     * @Route("/", name="agency_index", methods={"GET"})
     */
    public function index(AgencyRepository $agencyRepository): Response
    {
        return $this->render('backend/agency/index.html.twig', [
            'agencies' => $agencyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="agency_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $agency = new Agency();
        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = time().'-'.uniqid().'.'.$image->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('uploads_directory_agency'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $form->addError(new FormError($this->trans->trans('admin.image.upload.failed')));
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $agency->setImage($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agency);
            $entityManager->flush();

            return $this->redirectToRoute('agency_index');
        }

        return $this->render('backend/agency/new.html.twig', [
            'agency' => $agency,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agency_show", methods={"GET"})
     */
    public function show(Agency $agency): Response
    {
        return $this->render('backend/agency/show.html.twig', [
            'agency' => $agency,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="agency_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Agency $agency): Response
    {
        $form = $this->createForm(AgencyType::class, $agency);
        $oldImage = $agency->getImage();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $agency->setImage($oldImage);

            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $newFilename = time().'-'.uniqid().'.'.$image->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('uploads_directory_agency'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $form->addError(new FormError($this->trans->trans('admin.image.upload.failed')));
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $agency->setImage($newFilename);
                if (is_file($this->getParameter('uploads_directory_agency').$oldImage)){
                    unlink($this->getParameter('uploads_directory_agency').$oldImage);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('agency_index');
        }

        return $this->render('backend/agency/edit.html.twig', [
            'agency' => $agency,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="agency_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Agency $agency): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agency->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($agency);
            $entityManager->flush();
        }

        return $this->redirectToRoute('agency_index');
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Adverts;
use App\Entity\EstateCharacteristics;
use App\Form\AdvertsType;
use App\Repository\AdvertsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/adverts")
 */
class AdvertsController extends AbstractController
{

    private $entity_manager;
    private $trans;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entity_manager = $entityManager;
        $this->trans = $translator;
    }

    /**
     * @param AdvertsRepository $advertsRepository
     * @Route("/", name="adverts_index", methods={"GET"})
     * @return Response
     */
    public function index(AdvertsRepository $advertsRepository): Response
    {
        return $this->render('backend/adverts/index.html.twig', [
            'adverts' => $advertsRepository->findAll(),
        ]);
    }

    /**
     * @param AdvertsRepository $advertsRepository
     * @Route("/waiting", name="adverts_waiting", methods={"GET"})
     * @return Response
     */
    public function waiting(AdvertsRepository $advertsRepository): Response
    {
        $adverts = $advertsRepository->findBy(['valid' => false, 'refused' => false]);
        return $this->render('backend/adverts/waiting.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * @param AdvertsRepository $advertsRepository
     * @Route("/refused", name="adverts_refused", methods={"GET"})
     * @return Response
     */
    public function refused(AdvertsRepository $advertsRepository): Response
    {
        $adverts = $advertsRepository->findBy(['refused' => true]);
        return $this->render('backend/adverts/refused.html.twig', [
            'adverts' => $adverts,
        ]);
    }

    /**
     * @param Adverts $advert
     * @Route("/{id}", name="adverts_show", methods={"GET"})
     * @return Response
     */
    public function show(Adverts $advert): Response
    {
        $characteristic = null;
        $charIds = json_decode($advert->getCharacteristics(), true);
        if (is_array($charIds) && $charIds != null){
            $characteristic = $this->entity_manager->getRepository(EstateCharacteristics::class)->findByChars($charIds);
        }
        $images = array_diff(scandir(__DIR__.'/../../../public/uploads/'.$advert->getImages()), ['.', '..']);

        return $this->render('backend/adverts/show.html.twig', [
            'advert' => $advert,
            'characteristics' => $characteristic,
            'images' => $images,
        ]);
    }


    /**
     * @Route("/{id}", name="adverts_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Adverts $advert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('adverts_index');
    }


    private function _checkData()
    {
        $data = json_decode(file_get_contents('php://input'), TRUE);
        if ($data == null)
        {
            $response["code"] = -1;
            $response["response"] = ["message"=> $this->trans->trans('admin.form.empty')];
            return new Response(json_encode($response));
        }
        return $data;
    }
}

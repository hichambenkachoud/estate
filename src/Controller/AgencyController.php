<?php


namespace App\Controller;


use App\Entity\Agency;
use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AgencyController extends AbstractController
{
    private $trans;
    private $entityManager;
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager){
        $this->trans = $translator;
        $this->entityManager = $entityManager;
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/agency", name="front_agency_index", methods={"GET"})
     */
    public function index(Request $request)
    {
        $city = $name = null;
        if ($request->get('page') && !empty($request->get('page'))){
            $page = intval($request->get('page'));
        }else{
            $page = 1;
        }
        if ($request->get('city') && !empty($request->get('city'))){
            $city = $request->get('city');
        }
        if ($request->get('name') && !empty($request->get('name'))){
            $name = $request->get('name');
        }

        $agencies = $this->entityManager->getRepository(Agency::class)->findAllAgency($page, $city, $name, Agency::NUM_ITEMS);
        $total = count($agencies);
        $cities = $this->entityManager->getRepository(City::class)->findBy(['enabled' => true],  array('code'=>'ASC'));

        return $this->render('frontend/agency/index.html.twig', [
            'agencies' => $agencies,
            'cities' => $cities,
            'page_count' => ceil($total/Agency::NUM_ITEMS),
            'current_page' => $page,
            'total' => $total
        ]);
    }

}

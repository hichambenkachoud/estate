<?php


namespace App\Controller;


use App\Entity\Adverts;
use App\Entity\AdvertType;
use App\Entity\City;
use App\Entity\Country;
use App\Entity\EstateCharacteristics;
use App\Entity\EstateStatus;
use App\Entity\EstateType;
use App\Entity\Province;
use App\Entity\Quartier;
use App\Entity\Region;
use App\Form\AdvertsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdvertController extends AbstractController
{
    private $trans;
    private $entity_manager;
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->trans = $translator;
        $this->entity_manager = $entityManager;
    }

    const EXTENSIONS = ['jpg', 'png', 'jpeg'];

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route(path="/ajouter-nouvelle-annonce", name="front_advert_new", methods={"GET", "POST"})
     */
    public function add(Request $request)
    {
        $advert = new Adverts();


       /* $array = [
            'vente' => 'hicham',
        ];
        $fi = file_get_contents('../translations/messages.ru.yaml');
        $yaml = Yaml::dump($array);

        file_put_contents('../translations/messages.ru.yaml', $fi.$yaml);*/

       if ($request->isMethod(Request::METHOD_POST) && isset($_POST['adverts'])){
           $member = $this->getUser();
           $advertPost = $_POST['adverts'];
           $advert->setAdvertType($this->getAdvertType(intval($advertPost['advert_type'])));
           $advert->setEstateType($this->getEstateType(intval($advertPost['estateType'])));
           $advert->setEstateStatus($this->getEstateStatus(intval($advertPost['estate_status'])));
           $advert->setArea(floor($advertPost['area']));
           $advert->setPrice(floor($advertPost['price']));
           $advert->setRooms(isset($advertPost['rooms']) ? intval($advertPost['rooms']) : 0);
           $advert->setBathrooms(isset($advertPost['bathrooms']) ? intval($advertPost['bathrooms']) : 0);
           $advert->setBedrooms(isset($advertPost['bedrooms']) ? intval($advertPost['bedrooms']) : 0);
           $advert->setFloor(isset($advertPost['floor']) ? intval($advertPost['floor']) : 0);
           $advert->setSoilType(isset($advertPost['soil']) ? $advertPost['soil'] : null);
           $advert->setCapacity(isset($advertPost['capacity']) ? intval($advertPost['capacity']) : 0);
           $advert->setMinNight(isset($advertPost['min_night']) ? intval($advertPost['min_night']) : 0);
           $advert->setCharacteristics(isset($advertPost['characteristics']) ? json_encode($advertPost['characteristics']) : json_encode(null));
           $advert->setAddress(isset($advertPost['address']) ? $advertPost['address'] : null);
           $advert->setCountry($this->getCountry());
           $advert->setRegion($this->getRegion(isset($advertPost['region']) ? intval($advertPost['region']) : 0));
           $advert->setCity($this->getCity(isset($advertPost['city']) ? intval($advertPost['city']) : 0));
           $advert->setProvince($this->getProvince(isset($advertPost['province']) ? intval($advertPost['province']) : 0));
           $advert->setQuartier($this->getQuartier(isset($advertPost['quartier']) ? intval($advertPost['quartier']) : 0));
           $advert->setDescription(isset($advertPost['description']) ? $advertPost['description'] : null);
           $advert->setTitle(isset($advertPost['title']) ? $advertPost['title'] : null);
           $advert->setMobileNumber(isset($advertPost['tel']) ? $advertPost['tel'] : null);
           $advert->setAge(isset($advertPost['age']) ? $advertPost['age'] : null);
           $advert->setRentType(isset($advertPost['rentType']) ? $advertPost['rentType'] : null);
           $advert->setLongitude(isset($advertPost['longitude']) ? $advertPost['longitude'] : null);
           $advert->setLatitude(isset($advertPost['latitude']) ? $advertPost['latitude'] : null);
           $advert->setMember($member);
           $dirName = time().rand(0000,9999);
           $advert->setImages($dirName);
           $advert->setReference($dirName);


           try{
               $this->entity_manager->persist($advert);
               $this->entity_manager->flush();

               $countFiles = count($_FILES['adverts']['name']);
               $uploadsDir = $this->getParameter('uploads_directory');
               $newDirName = $uploadsDir.$dirName;
               if (!file_exists($newDirName)) {
                   mkdir($newDirName, 0777, true);
               }

               for($i=0; $i< $countFiles; $i++)
               {
                   $fileName = basename($_FILES['adverts']['name'][$i]);
                   $targetFilePath = $newDirName . '/' .$fileName;
                   $explodeFileName = explode('.', $fileName);
                   $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                   if (count($explodeFileName) > 2){
                       throw new \Exception('file.invalid');
                   }

                   if (!in_array($fileType, self::EXTENSIONS)){
                       throw new \Exception('file.extension.invalid');
                   }
                   $targetFilePath = $newDirName . '/' .$i.'.png';
                   move_uploaded_file($_FILES["adverts"]["tmp_name"][$i], $targetFilePath);
               }


               $this->addFlash('success', $this->trans->trans('front.advert.add.success'));
               //rename($newDirName, $uploadsDir.'hicham');
           }catch (\Exception $e){
               $this->addFlash('error', $e->getMessage());
           }

       }

        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characteristics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled'=>true]);
        $regions = $this->entity_manager->getRepository(Region::class)->findBy(['enabled'=>true]);
        return $this->render('frontend/advert/new.html.twig', [
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estatesStatus' => $estateStatus,
            'characteristics' => $characteristics,
            'regions' => $regions,
        ]);
    }


    /**
     * @param $city
     * @param $id
     * @param $page
     *@Route(path="/{city}-{id}/{page}", name="front_advert_by_city", methods={"GET", "POST"})
     * @return Response
     */
    public function getAdvertByCity($city,  $id, $page = 1)
    {
        /** @var City $advertCity */
        $advertCity = $this->entity_manager->getRepository(City::class)->find($id);

        if (!$advertCity){
            return $this->redirectToRoute('front_index');
        }elseif ($city != strtolower($advertCity->getCode())){
            return $this->redirectToRoute('front_index');
        }

        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characterstics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled' => true]);

        $adverts = $this->entity_manager->getRepository(Adverts::class)->findAdvertsByCity($advertCity, $page, Adverts::NUM_ITEMS);
        $total = count($adverts);


        return $this->render('frontend/advert/list-city.html.twig', [
            'advertCity' => $advertCity,
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'current_page' => $page,
            'chars' => $characterstics,
            'adverts' => $adverts,
            'page_count' => ceil($total/Adverts::NUM_ITEMS),
        ]);
    }

    /**
     * @param Request $request
     * @Route(path="/search", name="search-advert", methods={"GET"})
     * return Response|NULL
     */
    public function searchAdvert(Request $request){

        $advertTypeRequest = $rentTypeRequest = $estateTypeRequest = $priceMin = $priceMax = $areaMin = $areaMax = $publication = $sorted = null;
        $rooms = $advertStatusRequest = $floors = $adverts = $bathrooms = $characters = array();

        if ($request->get('page') && !empty($request->get('page'))){
            $page = intval($request->get('page'));
        }else{
            $page=1;
        }
        if ($request->get('advertType') && !empty($request->get('advertType'))){
            $advertTypeRequest = $request->get('advertType');
        }
        if ($request->get('rentType') && !empty($request->get('rentType'))){
            $rentTypeRequest = $request->get('rentType');
        }
        if ($request->get('estateType') && !empty($request->get('estateType'))){
            $estateTypeRequest = $request->get('estateType');
        }
        /** @var AdvertType $advertType */
        $advertType = $this->entity_manager->getRepository(AdvertType::class)->find(intval($advertTypeRequest));
        if ($advertType && strtolower($advertType->getCode()) == AdvertType::ADVERT_TYPE_SELL){
            if ($request->get('priceMin') && !empty($request->get('priceMin'))){
                $priceMin = intval($request->get('priceMin'));
            }
            if ($request->get('priceMax') && !empty($request->get('priceMax'))){
                $priceMax = intval($request->get('priceMax'));
            }
        }elseif ($advertType && strtolower($advertType->getCode()) == AdvertType::ADVERT_TYPE_SELL){
            if ($rentTypeRequest == AdvertType::RENT_DAY){
                if ($request->get('priceMinDay') && !empty($request->get('priceMinDay'))){
                    $priceMin = intval($request->get('priceMinDay'));
                }
                if ($request->get('priceMaxDay') && !empty($request->get('priceMaxDay'))){
                    $priceMax = intval($request->get('priceMaxDay'));
                }
            }elseif ($rentTypeRequest == AdvertType::RENT_MONTH){
                if ($request->get('priceMinMonth') && !empty($request->get('priceMinMonth'))){
                    $priceMin = intval($request->get('priceMinMonth'));
                }
                if ($request->get('priceMaxMonth') && !empty($request->get('priceMaxMonth'))){
                    $priceMax = intval($request->get('priceMaxMonth'));
                }
            }elseif ($rentTypeRequest == AdvertType::RENT_YEAR){
                if ($request->get('priceMinYear') && !empty($request->get('priceMinYear'))){
                    $priceMin = intval($request->get('priceMinYear'));
                }
                if ($request->get('priceMinYear') && !empty($request->get('priceMinYear'))){
                    $priceMax = intval($request->get('priceMinYear'));
                }
            }
        }

        if ($request->get('areaMin') && !empty($request->get('areaMin'))){
            $areaMin = $request->get('areaMin');
        }
        if ($request->get('areaMax') && !empty($request->get('areaMax'))){
            $areaMax = $request->get('areaMax');
        }
        if ($request->get('rooms') && !empty($request->get('rooms'))){
            $rooms = $request->get('rooms');
        }
        if ($request->get('bathrooms') && !empty($request->get('rooms'))){
            $bathrooms = $request->get('bathrooms');
        }
        if ($request->get('chars') && !empty($request->get('chars'))){
            $characters = $request->get('chars');
        }
        if ($request->get('status') && !empty($request->get('status'))){
            $advertStatusRequest = $request->get('status');
        }
        if ($request->get('floor') && !empty($request->get('floor'))){
            $floors = $request->get('floor');
        }
        if ($request->get('publication') && !empty($request->get('publication'))){
            $publication = $request->get('publication');
        }
        if ($request->get('sorted') && !empty($request->get('sorted'))){
            $sorted = $request->get('sorted');
        }

        if ($advertType && strtolower($advertType->getCode()) == AdvertType::ADVERT_TYPE_SELL){
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllSellByCriteria($estateTypeRequest, $priceMin, $priceMax, $areaMin, $areaMax, $publication, $advertStatusRequest, $rooms, $bathrooms, $characters, $floors, $sorted, $page, Adverts::NUM_ITEMS);
        }elseif ($advertType && strtolower($advertType->getCode()) == AdvertType::ADVERT_TYPE_RENT){
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllRentByCriteria($rentTypeRequest, $estateTypeRequest, $priceMin, $priceMax, $areaMin, $areaMax, $publication, $advertStatusRequest, $rooms, $bathrooms, $characters, $floors, $sorted, $page, Adverts::NUM_ITEMS);
        }else{
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllValid($page, Adverts::NUM_ITEMS);
        }



        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characterstics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled' => true]);

        //var_dump($adverts);die();
        $total = count($adverts);


        return $this->render('frontend/advert/search-advert.html.twig', [
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'current_page' => $page,
            'chars' => $characterstics,
            'adverts' => $adverts,
            'page_count' => ceil($total/Adverts::NUM_ITEMS),
            'total' => $total
        ]);
    }

    /**
     * @param Request $request
     * @param int $page
     * @return Response
     * @Route(path="/firstsearch/{page}", name="search-advert-home", methods={"GET"})
     */
    public function searchAdvertHome(Request $request, $page=1){

        $advertTypeRequest = $estateTypeRequest = $cityRequest = null;
        $adverts = array();

        if ($request->get('advertType') && !empty($request->get('advertType'))){
            $advertTypeRequest = $request->get('advertType');
        }

        if ($request->get('estateType') && !empty($request->get('estateType'))){
            $estateTypeRequest = $request->get('estateType');
        }
        if ($request->get('city') && !empty($request->get('city'))){
            $cityRequest = trim($request->get('city'));
        }

        /** @var AdvertType $advertType */
        $advertType = $this->entity_manager->getRepository(AdvertType::class)->find(intval($advertTypeRequest));

        /** @var EstateType $estateType */
        $estateType = $this->entity_manager->getRepository(EstateType::class)->find(intval($estateTypeRequest));

        if ($advertType){
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllValidHome($advertType->getId(), $estateType->getId(), $cityRequest, $page, Adverts::NUM_ITEMS);
        }


        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characterstics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled' => true]);

        //var_dump($adverts);die();
        $total = count($adverts);


        return $this->render('frontend/advert/search-advert.html.twig', [
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'current_page' => $page,
            'chars' => $characterstics,
            'adverts' => $adverts,
            'page_count' => ceil($total/Adverts::NUM_ITEMS),
            'total' => $total
        ]);
    }


    /**
     * @param Adverts $advert
     * @return Response
     * @Route(path="/estate/{id}", name="front_advert_single", methods={"GET"})
     */
    public function singleAdvert(Adverts $advert)
    {
        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);

        $characteristic = null;
        $charIds = json_decode($advert->getCharacteristics(), true);

        $characteristic = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled'=>true]);

        $images = array_diff(scandir(__DIR__.'/../../public/uploads/'.$advert->getImages()), ['.', '..']);


        return $this->render('frontend/advert/single-advert.html.twig',[
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'advert' => $advert,
            'images' => $images,
            'features' => $characteristic,
            'charIds' => $charIds,
        ]);
    }


    /**
     * @param Request $request
     * @param int $page
     * @param string $slug
     * @return Response
     * @Route(path="/rent/{city}/{cityId}/{estate}", name="search-advert-footer-rent", methods={"GET"})
     */
    public function searchAdvertFooterRent(Request $request, $city = '', $cityId = 1, $estate = null, $page=1){

        $adverts = array();
        $cityType = null;

        if ($request->get('page') && !empty($request->get('page'))){
            $page = intval($request->get('page'));
        }

        /** @var AdvertType $advertType */
        $advertType = $this->entity_manager->getRepository(AdvertType::class)->findOneBy(['code' => AdvertType::ADVERT_TYPE_RENT]);

        $cityObj = $this->entity_manager->getRepository(City::class)->find(intval($cityId));
        if ($cityObj && strtolower($cityObj->getCode() == $city)){
            $cityType = $cityObj;
        }

        /** @var EstateType $estateTypeR */
        $estateTypeR = $this->entity_manager->getRepository(EstateType::class)->findOneBy(['code' => strtolower($estate)]);

        if ($advertType && $cityType && $estateTypeR){
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllByCity($advertType->getId(), $estateTypeR->getId(), $cityType->getCode(), $page, Adverts::NUM_ITEMS);
        }else{
            return $this->redirectToRoute('search-advert');
        }


        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characterstics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled' => true]);

        //var_dump($adverts);die();
        $total = count($adverts);


        return $this->render('frontend/advert/search-advert-footer-rent.html.twig', [
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'current_page' => $page,
            'chars' => $characterstics,
            'adverts' => $adverts,
            'page_count' => ceil($total/Adverts::NUM_ITEMS),
            'total' => $total,
            'subject' => 'front.rent.'.$estate.'.'.$city
        ]);
    }

    /**
     * @param Request $request
     * @param int $page
     * @param string $slug
     * @return Response
     * @Route(path="/sell/{city}/{cityId}/{estate}", name="search-advert-footer-sell", methods={"GET"})
     */
    public function searchAdvertFooterSell(Request $request, $city = '', $cityId = 1, $estate = null, $page=1){

        $adverts = array();
        $cityType = null;

        if ($request->get('page') && !empty($request->get('page'))){
            $page = intval($request->get('page'));
        }
        /** @var AdvertType $advertType */
        $advertType = $this->entity_manager->getRepository(AdvertType::class)->findOneBy(['code' => AdvertType::ADVERT_TYPE_SELL]);

        $cityObj = $this->entity_manager->getRepository(City::class)->find(intval($cityId));
        if ($cityObj && strtolower($cityObj->getCode() == $city)){
            $cityType = $cityObj;
        }

        /** @var EstateType $estateTypeR */
        $estateTypeR = $this->entity_manager->getRepository(EstateType::class)->findOneBy(['code' => strtolower($estate)]);

        if ($advertType && $cityType && $estateTypeR){
            $adverts = $this->entity_manager->getRepository(Adverts::class)->findAllByCity($advertType->getId(), $estateTypeR->getId(), $cityType->getCode(), $page, Adverts::NUM_ITEMS);
        }else{
            return $this->redirectToRoute('search-advert');
        }


        $advertTypes = $this->entity_manager->getRepository(AdvertType::class)->findBy(['enabled' => true]);
        $estateTypes = $this->entity_manager->getRepository(EstateType::class)->findBy(['enabled' => true]);
        $estateStatus = $this->entity_manager->getRepository(EstateStatus::class)->findBy(['enabled' => true]);
        $characterstics = $this->entity_manager->getRepository(EstateCharacteristics::class)->findBy(['enabled' => true]);

        //var_dump($adverts);die();
        $total = count($adverts);


        return $this->render('frontend/advert/search-advert-footer-sell.html.twig', [
            'advertTypes' => $advertTypes,
            'estateTypes' => $estateTypes,
            'estateStatus' => $estateStatus,
            'city' => $cityType,
            'estateType' => $estateTypeR,
            'current_page' => $page,
            'chars' => $characterstics,
            'adverts' => $adverts,
            'page_count' => ceil($total/Adverts::NUM_ITEMS),
            'total' => $total,
            'subject' => 'front.sell.'.$estate.'.'.$city
        ]);
    }



    /**
     * @param $id
     * @return AdvertType|null
     */
    private function getAdvertType($id){
        return $this->entity_manager->getRepository(AdvertType::class)->find($id);
    }
    /**
     * @param $id
     * @return EstateType|null
     */
    private function getEstateType($id){
        return $this->entity_manager->getRepository(EstateType::class)->find($id);
    }
    /**
     * @param $id
     * @return EstateStatus|null
     */
    private function getEstateStatus($id){
        return $this->entity_manager->getRepository(EstateStatus::class)->find($id);
    }
    /**
     * @param $id
     * @return Region|null
     */
    private function getCountry(){
        return $this->entity_manager->getRepository(Country::class)->findOneBy(['isDefault' => true]);
    }
    /**
     * @param $id
     * @return Region|null
     */
    private function getRegion($id){
        return $this->entity_manager->getRepository(Region::class)->find($id);
    }
    /**
     * @param $id
     * @return City|null
     */
    private function getCity($id){
        return $this->entity_manager->getRepository(City::class)->find($id);
    }
    /**
     * @param $id
     * @return Province|null
     */
    private function getProvince($id){
        return $this->entity_manager->getRepository(Province::class)->find($id);
    }
    /**
     * @param $id
     * @return Quartier|null
     */
    private function getQuartier($id){
        return $this->entity_manager->getRepository(Quartier::class)->find($id);
    }
}

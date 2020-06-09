<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertsRepository")
 */
class Adverts
{

    const NUM_ITEMS = 10;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(name="age", type="string", nullable=true)
     */
    private $age;
    /**
     * @ORM\Column(name="area", type="integer", nullable=true)
     */
    private $area;

    /**
     * @ORM\Column(name="soil_type", type="string", nullable=true)
     */
    private $soilType;

    /**
     * @ORM\Column(name="rooms", type="integer", nullable=true)
     */
    private $rooms;

    /**
     * @ORM\Column(name="bedrooms", type="integer", nullable=true)
     */
    private $bedrooms;

    /**
     * @ORM\Column(name="bathrooms", type="integer", nullable=true)
     */
    private $bathrooms;

    /**
     * @ORM\Column(name="floor", type="integer", nullable=true)
     */
    private $floor;

    /**
     * @ORM\Column(name="capacity", type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(name="min_night", type="integer", nullable=true)
     */
    private $min_night;

    /**
     * @ORM\Column(name="mobile_number", type="string", length=45)
     */
    private $mobileNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AdvertType", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $advertType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EstateType", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $estateType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Members", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\Column(name="characteristics", type="string", nullable=true)
     */
    private $characteristics;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @ORM\Column(name="valid", type="boolean", nullable=true)
     */
    private $valid;

    /**
     * @ORM\Column(name="refused", type="boolean")
     */
    private $refused;

    /**
     * @ORM\Column(name="images", type="text")
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Province", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $province;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quartier", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $quartier;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EstateStatus", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $estateStatus;

    /**
     * @ORM\Column(name="rent_type", type="string", nullable=true)
     */
    private $rentType;

    /**
     * @ORM\Column(name="reference", type="string", nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $latitude;



    public function __construct()
    {
        $this->createDate = new \DateTime('now', new \DateTimeZone('Africa/Casablanca'));
        $this->valid = false;
        $this->refused = false;
        $this->reference = time().rand(0000,9999);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(?int $bathrooms): self
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getMinNight(): ?int
    {
        return $this->min_night;
    }

    public function setMinNight(?int $min_night): self
    {
        $this->min_night = $min_night;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    public function getAdvertType(): ?AdvertType
    {
        return $this->advertType;
    }

    public function setAdvertType(?AdvertType $advertType): self
    {
        $this->advertType = $advertType;

        return $this;
    }

    public function getEstateType(): ?EstateType
    {
        return $this->estateType;
    }

    public function setEstateType(?EstateType $estateType): self
    {
        $this->estateType = $estateType;

        return $this;
    }

    public function getCharacteristics(): ?string
    {
        return $this->characteristics;
    }

    public function setCharacteristics(?string $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getRefused(): ?bool
    {
        return $this->refused;
    }

    public function setRefused(bool $refused): self
    {
        $this->refused = $refused;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param mixed $member
     */
    public function setMember($member): void
    {
        $this->member = $member;
    }

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area): void
    {
        $this->area = $area;
    }

    /**
     * @return mixed
     */
    public function getSoilType()
    {
        return $this->soilType;
    }

    /**
     * @param mixed $soilType
     */
    public function setSoilType($soilType): void
    {
        $this->soilType = $soilType;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region): void
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param mixed $province
     */
    public function setProvince($province): void
    {
        $this->province = $province;
    }

    /**
     * @return mixed
     */
    public function getQuartier()
    {
        return $this->quartier;
    }

    /**
     * @param mixed $quartier
     */
    public function setQuartier($quartier): void
    {
        $this->quartier = $quartier;
    }

    /**
     * @return mixed
     */
    public function getEstateStatus()
    {
        return $this->estateStatus;
    }

    /**
     * @param mixed $estateStatus
     */
    public function setEstateStatus($estateStatus): void
    {
        $this->estateStatus = $estateStatus;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getRentType()
    {
        return $this->rentType;
    }

    /**
     * @param mixed $rentType
     */
    public function setRentType($rentType): void
    {
        $this->rentType = $rentType;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }


}

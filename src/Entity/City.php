<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string", length=50)
     */
    private $code;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Province", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $province;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quartier", mappedBy="city", orphanRemoval=true)
     */
    private $quartiers;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Adverts", mappedBy="city")
     */
    private $adverts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Agency", mappedBy="city")
     */
    private $agencies;

    public function __construct()
    {
        $this->quartiers = new ArrayCollection();
        $this->createDate = new \DateTime('now', new \DateTimeZone('Africa/Casablanca'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): self
    {
        $this->province = $province;

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

    /**
     * @return Collection|Quartier[]
     */
    public function getQuartiers(): Collection
    {
        return $this->quartiers;
    }

    public function addQuartier(Quartier $quartier): self
    {
        if (!$this->quartiers->contains($quartier)) {
            $this->quartiers[] = $quartier;
            $quartier->setCity($this);
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): self
    {
        if ($this->quartiers->contains($quartier)) {
            $this->quartiers->removeElement($quartier);
            // set the owning side to null (unless already changed)
            if ($quartier->getCity() === $this) {
                $quartier->setCity(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Adverts[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Adverts $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
            $advert->setCity($this);
        }

        return $this;
    }

    public function removeAdvert(Adverts $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);
            // set the owning side to null (unless already changed)
            if ($advert->getCity() === $this) {
                $advert->setCity(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Agency[]
     */
    public function getAgency(): Collection
    {
        return $this->agencies;
    }

    public function addAgency(Agency $agency): self
    {
        if (!$this->agencies->contains($agency)) {
            $this->agencies[] = $agency;
            $agency->setCity($this);
        }

        return $this;
    }

    public function removeAgency(Agency $agency): self
    {
        if ($this->agencies->contains($agency)) {
            $this->agencies->removeElement($agency);
            // set the owning side to null (unless already changed)
            if ($agency->getCity() === $this) {
                $agency->setCity(null);
            }
        }

        return $this;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
          'id' => $this->id,
          'code' => $this->code,
          'name' => $this->code
        ];
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembersRepository")
 * @UniqueEntity(fields = "email", message="email_existe")
 */
class Members implements UserInterface, \Serializable
{

    const MR_TYPE = 'mr';
    const MME_TYPE = 'mme';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="admin.firstname.notblank")
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="admin.lastname.notblank")
     */
    private $lastName;

    /**
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     * @Assert\NotBlank(message="admin.email.notblank")
     */
    private $email;

    /**
     * @ORM\Column(name="mobile_number", type="string", length=100, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^212(5|6|7)\d{8}$/", message="mobilenumber.format"
     * )
     */
    private $mobileNumber;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(name="birth_day", type="datetime", nullable=true)
     */
    private $birthDay;

    /**
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @ORM\Column(name="username", type="string", length=100, nullable=true, unique=true)
     */
    private $userName;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="admin.password.notblank", groups={"create"})
     * @Assert\Regex(
     *     pattern = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/", message="front.password.format"
     * )
     */
    private $password;

    /**
     * @ORM\Column(name="genre", type="string", length=20, nullable=true)
     */
    private $genre;

    /**
     * @var $resetToken
     * @ORM\Column(name="reset_token", type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @var $confirmationToken
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var $adverts
     * @ORM\OneToMany(targetEntity="App\Entity\Adverts", mappedBy="member")
     */
    private $adverts;

    /**
     * @var $image
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    public function __construct()
    {
        $this->createDate = new \DateTime('now', new \DateTimeZone('Africa/Casablanca'));
        $this->enabled = false;
        $this->adverts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

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

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreatedate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUsername($username): self
    {
        $this->userName = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @return mixed
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * @param mixed $resetToken
     */
    public function setResetToken($resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @return mixed
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param mixed $confirmationToken
     */
    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
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
            $advert->setEstateType($this);
        }

        return $this;
    }

    public function removeAdvert(Adverts $advert): self
    {
        if ($this->adverts->contains($advert)) {
            $this->adverts->removeElement($advert);
            // set the owning side to null (unless already changed)
            if ($advert->getEstateType() === $this) {
                $advert->setEstateType(null);
            }
        }

        return $this;
    }



    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->userName,
            $this->email,
            $this->password,
        ));
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->userName,
            $this->email,
            $this->password,
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function isValidTel($tel)
    {
        return preg_match("/^212(5|6|7)\d{8}$/", $tel) === 1;
    }

    public function isValidPassword($password)
    {
        return preg_match("/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/", $password) === 1;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }


}

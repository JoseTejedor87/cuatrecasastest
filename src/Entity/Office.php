<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficeRepository")
 */
class Office extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $cp;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img_map;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link_google;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="office", cascade={"persist"}, orphanRemoval=true)
     */
    private $img_office;


    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $place;

    /**
     * @ORM\Column(type="integer")
     */
    private $geographical_area;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sap;

    /**
     * @Gedmo\Slug(fields={"city", "address"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lawyer", mappedBy="office", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $lawyer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

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

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getImgMap(): ?string
    {
        return $this->img_map;
    }

    public function setImgMap(string $img_map): self
    {
        $this->img_map = $img_map;

        return $this;
    }

    public function getLinkGoogle(): ?string
    {
        return $this->link_google;
    }

    public function setLinkGoogle(string $link_google): self
    {
        $this->link_google = $link_google;

        return $this;
    }

    public function getImgOffice(): ?string
    {
        return $this->img_office;
    }


    public function setImgOffice(?Resource $img_office): self
    {
        $this->img_office = $img_office;
        if ($img_office) {
            $img_office->setOffice($this);
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(int $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getGeographicalArea(): ?int
    {
        return $this->geographical_area;
    }

    public function setGeographicalArea(int $geographical_area): self
    {
        $this->geographical_area = $geographical_area;

        return $this;
    }

    public function getSap(): ?string
    {
        return $this->sap;
    }

    public function setSap(string $sap): self
    {
        $this->sap = $sap;

        return $this;
    }
    
    /**
     * @return Collection|Lawyer[]
     */
    public function getLawyers(): Collection
    {
        return $this->lawyer;
    }

    public function addLawyer(Lawyer $lawyer): self
    {
        if (!$this->lawyer->contains($lawyer)) {
            $this->lawyer[] = $lawyer;
            $lawyer->setOffice($this);
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyer->contains($lawyer)) {
            $this->lawyer->removeElement($lawyer);
            // set the owning side to null (unless already changed)
            if ($lawyer->getOffice() === $this) {
                $lawyer->setOffice(null);
            }
        }

        return $this;
    }
}

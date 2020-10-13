<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $cp;


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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lng;


    /**
     * @Gedmo\Slug(fields={"address"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lawyer", mappedBy="office", cascade={"persist"}, orphanRemoval=true)
     */
    private $lawyer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="office", cascade={"persist"}, orphanRemoval=true)
     */
    private $event;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", mappedBy="offices")
     */
    private $publication;

    public function __construct()
    {
        $this->lawyer = new ArrayCollection();
        $this->event = new ArrayCollection();
        $this->publication = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->translate('es')->getCity();
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

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImgOffice(): ?Resource
    {
        return $this->img_office;
    }

    public function setImgOffice(?Resource $img_office): self
    {
        $this->img_office = $img_office;

        // set (or unset) the owning side of the relation if necessary
        $newOffice = null === $img_office ? null : $this;
        if ($img_office->getOffice() !== $newOffice) {
            $img_office->setOffice($newOffice);
        }

        return $this;
    }

    /**
     * @return Collection|Lawyer[]
     */
    public function getLawyer(): Collection
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

    /**
     * @return Collection|Event[]
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->event->contains($event)) {
            $this->event[] = $event;
            $event->setOffice($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->event->contains($event)) {
            $this->event->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getOffice() === $this) {
                $event->setOffice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublication(): Collection
    {
        return $this->publication;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publication->contains($publication)) {
            $this->publication[] = $publication;
            $publication->addOffice($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->contains($publication)) {
            $this->publication->removeElement($publication);
            $publication->removeOffice($this);
        }

        return $this;
    }



}

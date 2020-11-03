<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region extends Publishable
{

    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $principal;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Office", inversedBy="region")
     */
    private $office;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Award", mappedBy="region")
     */
    private $award;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="region", cascade={"persist"}, orphanRemoval=true)
     */
    private $photo;


    public function __construct()
    {
        $this->office = new ArrayCollection();
        $this->award = new ArrayCollection();
    }


    public function getPrincipal(): ?bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): self
    {
        $this->principal = $principal;

        return $this;
    }

    public function getPhoto(): ?Resource
    {
        return $this->photo;
    }

    public function setPhoto(?Resource $photo): self
    {
        $this->photo = $photo;

        // set (or unset) the owning side of the relation if necessary
        $newRegion = null === $photo ? null : $this;
        if ($photo->getRegion() !== $newRegion) {
            $photo->setRegion($newRegion);
        }

        return $this;
    }

    /**
     * @return Collection|Award[]
     */
    public function getAward(): Collection
    {
        return $this->award;
    }

    public function addAward(Award $award): self
    {
        if (!$this->award->contains($award)) {
            $this->award[] = $award;
        }

        return $this;
    }

    public function removeAward(Award $award): self
    {
        if ($this->award->contains($award)) {
            $this->award->removeElement($award);
        }

        return $this;
    }

    /**
     * @return Collection|Office[]
     */
    public function getOffice(): Collection
    {
        return $this->office;
    }

    public function addOffice(Office $office): self
    {
        if (!$this->offices->contains($office)) {
            $this->offices[] = $office;
        }

        return $this;
    }

    public function removeOffice(Office $office): self
    {
        if ($this->offices->contains($office)) {
            $this->offices->removeElement($office);
        }

        return $this;
    }

}
<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
//use Doctrine\Common\Collections\ArrayCollection;
//use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SliderRepository")
 */
class Slider extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="slider", cascade={"persist"}, orphanRemoval=true)
     */
    private $image;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Banner", mappedBy="sliders", cascade={"persist"})
     */      
    private $banners;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Office", inversedBy="sliders")
     */
    private $offices;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
        $this->offices = new ArrayCollection();
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getImage(): ?Resource
    {
        return $this->image;
    }

    public function setImage(?Resource $image): self
    {
        $this->image = $image;

        // set (or unset) the owning side of the relation if necessary
        $newSlider = null === $image ? null : $this;
        if ($image->getSlider() !== $newSlider) {
            $image->setSlider($newSlider);
        }

        return $this;
    }

    /**
     * @return Collection|Banner[]
     */
    public function getBanners(): Collection
    {
        return $this->banners;
    }

    public function addBanner(Banner $banner): self
    {
        if (!$this->banners->contains($banner)) {
            $this->banners[] = $banner;
            $banner->addSlider($this);
        }

        return $this;
    }

    public function removeBanner(Banner $banner): self
    {
        if ($this->banners->contains($banner)) {
            $this->banners->removeElement($banner);
            $banner->removeSlider($this);
        }

        return $this;
    }

    /**
     * @return Collection|Office[]
     */
    public function getOffices(): Collection
    {
        return $this->offices;
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

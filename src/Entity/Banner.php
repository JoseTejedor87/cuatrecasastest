<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BannerRepository")
 */
class Banner extends Item
{

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Slider", inversedBy="banners" , cascade={"persist"}, orphanRemoval=true)
     */      
    private $sliders;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $location;

    /**
     * @ORM\Column(type="integer" , options={"default": "800"})
     */
    private $speed;
    
        /**
     * @ORM\Column(type="integer" , options={"default": "5000"})
     */
    private $delay;

    public function __construct()
    {
        $this->sliders = new ArrayCollection();
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getDelay(): ?int
    {
        return $this->delay;
    }

    public function setDelay(int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return Collection|Slider[]
     */
    public function getSliders(): Collection
    {
        return $this->sliders;
    }

    public function addSlider(Slider $slider): self
    {
        if (!$this->sliders->contains($slider)) {
            $this->sliders[] = $slider;
        }

        return $this;
    }

    public function removeSlider(Slider $slider): self
    {
        if ($this->sliders->contains($slider)) {
            $this->sliders->removeElement($slider);
        }

        return $this;
    }




}

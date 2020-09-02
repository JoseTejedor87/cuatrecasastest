<?php

namespace App\Entity;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="slider", cascade={"persist"}, orphanRemoval=true)
     */
    private $image;

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


}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 */
class Brand extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Home", inversedBy="brand")
     * @ORM\JoinColumn(nullable=true)
     */
    private $home;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="brand", cascade={"persist"}, orphanRemoval=true)
     */
    private $image;

    public function getHome(): ?Home
    {
        return $this->home;
    }

    public function setHome(?Home $home): self
    {
        $this->home = $home;

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
        $newBrand = null === $image ? null : $this;
        if ($image->getBrand() !== $newBrand) {
            $image->setBrand($newBrand);
        }

        return $this;
    }

    

}

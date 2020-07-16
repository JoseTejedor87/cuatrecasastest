<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AwardRepository")
 */
class Award extends Item
{
    use ORMBehaviors\Translatable\Translatable;


    /**
     * @ORM\Column(type="date")
     */
    private $year;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="award", cascade={"persist"}, orphanRemoval=true)
     */
    private $image;

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(\DateTimeInterface $year): self
    {
        $this->year = $year;

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
        $newAward = null === $image ? null : $this;
        if ($image->getAward() !== $newAward) {
            $image->setAward($newAward);
        }

        return $this;
    }


}

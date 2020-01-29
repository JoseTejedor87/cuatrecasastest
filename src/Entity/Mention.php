<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use App\Entity\Item;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MentionRepository")
 */
class Mention extends Item
{

    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="mentions")
     */
    private $lawyers;

    public function __construct()
    {
        $this->lawyers = new ArrayCollection();
    }

    /**
     * @return Collection|Lawyer[]
     */
    public function getLawyers(): Collection
    {
        return $this->lawyers;
    }

    public function addLawyer(Lawyer $lawyer): self
    {
        if (!$this->lawyers->contains($lawyer)) {
            $this->lawyers[] = $lawyer;
            $lawyer->addMention($this);
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyers->contains($lawyer)) {
            $this->lawyers->removeElement($lawyer);
            $lawyer->removeMention($this);
        }

        return $this;
    }
}

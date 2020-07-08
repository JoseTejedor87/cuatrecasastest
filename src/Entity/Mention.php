<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MentionRepository")
 */
class Mention extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lawyer", inversedBy="mentions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $lawyer;


    public function getLawyer(): ?Lawyer
    {
        return $this->lawyer;
    }

    public function setLawyer(?Lawyer $lawyer): self
    {
        $this->lawyer = $lawyer;

        return $this;
    }
}

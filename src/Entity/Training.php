<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
 */
class Training extends Item
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lawyer", inversedBy="trainings")
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

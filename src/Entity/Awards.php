<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AwardsRepository")
 */
class Awards extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;


    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="award", cascade={"persist"}, orphanRemoval=true)
     */
    private $img_office;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }


    public function getImgOffice(): ?Resource
    {
        return $this->img_office;
    }


    public function setImgOffice(?Resource $img_office): self
    {
        $this->img_office = $img_office;
        if ($img_office) {
            $img_office->setAward($this);
        }
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


}

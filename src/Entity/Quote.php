<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuoteRepository")
 */
class Quote extends Item
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $year;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", mappedBy="quote")
     */
    private $activities;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CaseStudy", mappedBy="quote")
     */
    private $caseStudy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Home", inversedBy="quotes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $home;    

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->caseStudy = new ArrayCollection();
    }


    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->addQuote($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            $activity->removeQuote($this);
        }

        return $this;
    }

    /**
     * @return Collection|CaseStudy[]
     */
    public function getCaseStudy(): Collection
    {
        return $this->caseStudy;
    }

    public function addCaseStudy(CaseStudy $caseStudy): self
    {
        if (!$this->caseStudy->contains($caseStudy)) {
            $this->caseStudy[] = $caseStudy;
            $caseStudy->addQuote($this);
        }

        return $this;
    }

    public function removeCaseStudy(CaseStudy $caseStudy): self
    {
        if ($this->caseStudy->contains($caseStudy)) {
            $this->caseStudy->removeElement($caseStudy);
            $caseStudy->removeQuote($this);
        }

        return $this;
    }

    public function getHome(): ?Home
    {
        return $this->home;
    }

    public function setHome(?Home $home): self
    {
        $this->home = $home;

        return $this;
    }

   
}

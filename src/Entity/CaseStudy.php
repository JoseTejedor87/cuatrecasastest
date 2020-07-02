<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CaseStudyRepository")
 */
class CaseStudy extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="caseStudy", cascade={"persist"}, orphanRemoval=true)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", inversedBy="caseStudies")
     */
    private $lawyers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="caseStudies")
     */
    private $activities;

    /**
     * Many cases have Many cases.
     * @ORM\ManyToMany(targetEntity="CaseStudy", mappedBy="relatedCaseStudies", cascade={"persist"})
     */
    private $relatedCaseStudiesWithMe;

    /**
     * Many cases have many cases.
     * @ORM\ManyToMany(targetEntity="CaseStudy", inversedBy="relatedCaseStudiesWithMe", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="casestudy_casestudy",
     *     joinColumns={@ORM\JoinColumn(name="casestudy_source", referencedColumnName="id", onDelete="NO ACTION")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="casestudy_target", referencedColumnName="id", onDelete="NO ACTION")}
     * )
     */
    private $relatedCaseStudies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Quote", inversedBy="caseStudy")
     */
    private $quote;
    

    public function __construct()
    {
        $this->lawyers = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->quote = new ArrayCollection();
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
            $lawyer->addCaseStudy($this);
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyers->contains($lawyer)) {
            $this->lawyers->removeElement($lawyer);
            $lawyer->removeCaseStudy($this);
        }

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
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

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
        $newCaseStudy = null === $image ? null : $this;
        if ($image->getCaseStudy() !== $newCaseStudy) {
            $image->setCaseStudy($newCaseStudy);
        }

        return $this;
    }

    /**
     * @return Collection|Quote[]
     */
    public function getQuote(): Collection
    {
        return $this->quote;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quote->contains($quote)) {
            $this->quote[] = $quote;
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quote->contains($quote)) {
            $this->quote->removeElement($quote);
        }

        return $this;
    }
}

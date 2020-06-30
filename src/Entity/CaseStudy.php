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

    public function __construct()
    {
        $this->relatedCaseStudiesWithMe = new ArrayCollection();
        $this->relatedCaseStudies = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->activities = new ArrayCollection();
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
     * @return Collection|CaseStudy[]
     */
    public function getRelatedCaseStudiesWithMe(): Collection
    {
        return $this->relatedCaseStudiesWithMe;
    }

    public function addRelatedCaseStudiesWithMe(CaseStudy $relatedCaseStudiesWithMe): self
    {
        if (!$this->relatedCaseStudiesWithMe->contains($relatedCaseStudiesWithMe)) {
            $this->relatedCaseStudiesWithMe[] = $relatedCaseStudiesWithMe;
            $relatedCaseStudiesWithMe->addRelatedCaseStudy($this);
        }

        return $this;
    }

    public function removeRelatedCaseStudiesWithMe(CaseStudy $relatedCaseStudiesWithMe): self
    {
        if ($this->relatedCaseStudiesWithMe->contains($relatedCaseStudiesWithMe)) {
            $this->relatedCaseStudiesWithMe->removeElement($relatedCaseStudiesWithMe);
            $relatedCaseStudiesWithMe->removeRelatedCaseStudy($this);
        }

        return $this;
    }

    /**
     * @return Collection|CaseStudy[]
     */
    public function getRelatedCaseStudies(): Collection
    {
        return $this->relatedCaseStudies;
    }

    public function addRelatedCaseStudy(CaseStudy $relatedCaseStudy): self
    {
        if (!$this->relatedCaseStudies->contains($relatedCaseStudy)) {
            $this->relatedCaseStudies[] = $relatedCaseStudy;
        }

        return $this;
    }

    public function removeRelatedCaseStudy(CaseStudy $relatedCaseStudy): self
    {
        if ($this->relatedCaseStudies->contains($relatedCaseStudy)) {
            $this->relatedCaseStudies->removeElement($relatedCaseStudy);
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
}

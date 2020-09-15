<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use App\Entity\Publishable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InsightRepository")
 */
class Insight extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $headerType;

    /**
     * @ORM\ManyToMany(targetEntity="Insight", mappedBy="relatedInsights", cascade={"persist"})
     */
    private $relatedInsightsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="Insight", inversedBy="relatedInsightsWithMe", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="insight_insight",
     *     joinColumns={@ORM\JoinColumn(name="insight_source", referencedColumnName="id", onDelete="NO ACTION")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="insight_target", referencedColumnName="id", onDelete="NO ACTION")}
     * )
     */
    private $relatedInsights;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="insights")
     */
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", inversedBy="insights")
     */
    private $publications;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showIntroBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showKnowledgeBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showEventsBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showLegalNoveltiesBlock;

    /**
         * @ORM\Column(type="boolean", nullable=false)
         */
    private $showCaseStudiesBlock;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", inversedBy="insights")
     */
    private $lawyers;

    public function __construct()
    {
        $this->relatedInsightsWithMe = new ArrayCollection();
        $this->relatedInsights = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    /**
     * @return Collection|Insight[]
     */
    public function getRelatedInsightsWithMe(): Collection
    {
        return $this->relatedInsightsWithMe;
    }

    public function addRelatedInsightsWithMe(Insight $relatedInsightsWithMe): self
    {
        if (!$this->relatedInsightsWithMe->contains($relatedInsightsWithMe)) {
            $this->relatedInsightsWithMe[] = $relatedInsightsWithMe;
            $relatedInsightsWithMe->addRelatedInsight($this);
        }

        return $this;
    }

    public function removeRelatedInsightsWithMe(Insight $relatedInsightsWithMe): self
    {
        if ($this->relatedInsightsWithMe->contains($relatedInsightsWithMe)) {
            $this->relatedInsightsWithMe->removeElement($relatedInsightsWithMe);
            $relatedInsightsWithMe->removeRelatedInsight($this);
        }

        return $this;
    }

    /**
     * @return Collection|Insight[]
     */
    public function getRelatedInsights(): Collection
    {
        return $this->relatedInsights;
    }

    public function addRelatedInsight(Insight $relatedInsight): self
    {
        if (!$this->relatedInsights->contains($relatedInsight)) {
            $this->relatedInsights[] = $relatedInsight;
        }

        return $this;
    }

    public function removeRelatedInsight(Insight $relatedInsight): self
    {
        if ($this->relatedInsights->contains($relatedInsight)) {
            $this->relatedInsights->removeElement($relatedInsight);
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
            $lawyer->addInsight($this);
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyers->contains($lawyer)) {
            $this->lawyers->removeElement($lawyer);
            $lawyer->removeInsight($this);
        }

        return $this;
    }

    public function getShowKnowledgeBlock(): ?bool
    {
        return $this->showKnowledgeBlock;
    }

    public function setShowKnowledgeBlock(bool $showKnowledgeBlock): self
    {
        $this->showKnowledgeBlock = $showKnowledgeBlock;

        return $this;
    }

    public function getShowEventsBlock(): ?bool
    {
        return $this->showEventsBlock;
    }

    public function setShowEventsBlock(bool $showEventsBlock): self
    {
        $this->showEventsBlock = $showEventsBlock;

        return $this;
    }

    public function getShowLegalNoveltiesBlock(): ?bool
    {
        return $this->showLegalNoveltiesBlock;
    }

    public function setShowLegalNoveltiesBlock(bool $showLegalNoveltiesBlock): self
    {
        $this->showLegalNoveltiesBlock = $showLegalNoveltiesBlock;

        return $this;
    }

    public function getShowCaseStudiesBlock(): ?bool
    {
        return $this->showCaseStudiesBlock;
    }

    public function setShowCaseStudiesBlock(bool $showCaseStudiesBlock): self
    {
        $this->showCaseStudiesBlock = $showCaseStudiesBlock;

        return $this;
    }

    public function getHeaderType(): ?string
    {
        return $this->headerType;
    }

    public function setHeaderType(string $headerType): self
    {
        $this->headerType = $headerType;

        return $this;
    }

    public function getShowIntroBlock(): ?bool
    {
        return $this->showIntroBlock;
    }

    public function setShowIntroBlock(bool $showIntroBlock): self
    {
        $this->showIntroBlock = $showIntroBlock;

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications[] = $publication;
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->contains($publication)) {
            $this->publications->removeElement($publication);
        }

        return $this;
    }
}

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", mappedBy="insights")
     */
    private $publications;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="insights")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CaseStudy", mappedBy="insights")
     */
    private $caseStudies;

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
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showTeamsBlock;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", inversedBy="insights")
     */
    private $lawyers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Home", inversedBy="insights")
     * @ORM\JoinColumn(nullable=true)
     */
    private $home;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="insight", cascade={"persist"}, orphanRemoval=true)
     */
    private $attachments;    

    public function __construct()
    {
        $this->relatedInsightsWithMe = new ArrayCollection();
        $this->relatedInsights = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->caseStudies = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->attachments = new ArrayCollection();
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

    /**
     * @return Collection|CaseStudy[]
     */
    public function getCaseStudies(): Collection
    {
        return $this->caseStudies;
    }

    public function addCaseStudy(CaseStudy $caseStudy): self
    {
        if (!$this->caseStudies->contains($caseStudy)) {
            $this->caseStudies[] = $caseStudy;
        }

        return $this;
    }

    public function removeCaseStudies(CaseStudy $caseStudy): self
    {
        if ($this->caseStudies->contains($caseStudy)) {
            $this->caseStudies->removeElement($caseStudy);
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
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
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyers->contains($lawyer)) {
            $this->lawyers->removeElement($lawyer);
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

    public function removeCaseStudy(CaseStudy $caseStudy): self
    {
        if ($this->caseStudies->contains($caseStudy)) {
            $this->caseStudies->removeElement($caseStudy);
            $caseStudy->removeInsight($this);
        }

        return $this;
    }

    /**
     * @return Collection|Resource[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Resource $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setInsight($this);
        }

        return $this;
    }

    public function removeAttachment(Resource $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getInsight() === $this) {
                $attachment->setInsight(null);
            }
        }

        return $this;
    }

    public function getShowTeamsBlock(): ?bool
    {
        return $this->showTeamsBlock;
    }

    public function setShowTeamsBlock(bool $showTeamsBlock): self
    {
        $this->showTeamsBlock = $showTeamsBlock;

        return $this;
    }

}

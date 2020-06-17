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
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="caseStudies")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="caseStudies")
     */
    private $lawyers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="caseStudies")
     */
    private $articles;

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
        $this->events = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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
            $event->addCaseStudy($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeCaseStudy($this);
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
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addCaseStudy($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removeCaseStud($this);
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

}

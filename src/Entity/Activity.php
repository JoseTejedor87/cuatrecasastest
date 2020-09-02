<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"sector" = "Sector", "practice" = "Practice", "desk" = "Desk", "product" = "Product"})
 *
 */
abstract class Activity extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $highlighted;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="activities")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="activities")
     */
    private $lawyers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Articles", mappedBy="activities")
     */
    private $Articles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="activity", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocks;
    private $lawyers_secondary;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="secondaryActivities")
     */
    private $key_contacts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CaseStudy", mappedBy="activities")
     */
    private $caseStudies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", mappedBy="activities")
     */
    private $publication;

    /**
     * Many activities have Many activities.
     * @ORM\ManyToMany(targetEntity="Activity", mappedBy="relatedActivities", cascade={"persist"})
     */
    private $relatedActivitiesWithMe;

    /**
     * Many activities have many activities.
     * @ORM\ManyToMany(targetEntity="Activity", inversedBy="relatedActivitiesWithMe", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="activity_activity",
     *     joinColumns={@ORM\JoinColumn(name="activity_source", referencedColumnName="id", onDelete="NO ACTION")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="activity_target", referencedColumnName="id", onDelete="NO ACTION")}
     * )
     */
    private $relatedActivities;

    /**
     * A parent has many children and a child has many parents
     * @ORM\ManyToMany(targetEntity="Activity", mappedBy="children", cascade={"persist"})
     */
    private $parents;

    /**
     * A parent has many children and a child has many parents
     * @ORM\ManyToMany(targetEntity="Activity", inversedBy="parents", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="activity_activity_parents",
     *     joinColumns={@ORM\JoinColumn(name="activity_parent", referencedColumnName="id", onDelete="NO ACTION")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="activity_children", referencedColumnName="id", onDelete="NO ACTION")}
     * )
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Insight", mappedBy="activities")
     */
    private $insights;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Quote", inversedBy="activities")
     */
    private $quote;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->blocks = new ArrayCollection();
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

    public function getHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    public function setHighlighted(bool $highlighted): self
    {
        $this->highlighted = $highlighted;

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
            $event->addActivity($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removeActivity($this);
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
            $lawyer->addActivity($this);
        }

        return $this;
    }

    public function removeLawyer(Lawyer $lawyer): self
    {
        if ($this->lawyers->contains($lawyer)) {
            $this->lawyers->removeElement($lawyer);
            $lawyer->removeActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addActivity($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removeActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setActivity($this);
        }

        return $this;
    }

    public function removeBlock(Block $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            // set the owning side to null (unless already changed)
            if ($block->getActivity() === $this) {
                $block->setActivity(null);
            }
        }

        return $this;
    }
}

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="secondaryActivities")
     */
    private $lawyers_secondary;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="activities")
     */
    private $Article;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="activity", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocks;

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
     * One Activity has Many Sub-Activities.
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="parent")
     */
    private $children;

    /**
     * Many Activities have One Activity as a Parent.
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="children")
     */
    private $parent;


    public function __construct()
    {
        $this->relatedActivitiesWithMe = new ArrayCollection();
        $this->relatedActivities = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->blocks = new ArrayCollection();
        $this->lawyers_secondary = new ArrayCollection();
        $this->Article = new ArrayCollection();
        $this->children = new ArrayCollection();
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
     * @return Collection|Article[]
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->article->contains($article)) {
            $this->article[] = $article;
            $article->addActivity($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->article->contains($article)) {
            $this->article->removeElement($article);
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

    /**
     * @return Collection|Lawyer[]
     */
    public function getLawyersSecondary(): Collection
    {
        return $this->lawyers_secondary;
    }

    public function addLawyersSecondary(Lawyer $lawyersSecondary): self
    {
        if (!$this->lawyers_secondary->contains($lawyersSecondary)) {
            $this->lawyers_secondary[] = $lawyersSecondary;
            $lawyersSecondary->addSecondaryActivity($this);
        }

        return $this;
    }

    public function removeLawyersSecondary(Lawyer $lawyersSecondary): self
    {
        if ($this->lawyers_secondary->contains($lawyersSecondary)) {
            $this->lawyers_secondary->removeElement($lawyersSecondary);
            $lawyersSecondary->removeSecondaryActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getRelatedActivitiesWithMe(): Collection
    {
        return $this->relatedActivitiesWithMe;
    }

    public function addRelatedActivitiesWithMe(Activity $relatedActivitiesWithMe): self
    {
        if (!$this->relatedActivitiesWithMe->contains($relatedActivitiesWithMe)) {
            $this->relatedActivitiesWithMe[] = $relatedActivitiesWithMe;
            $relatedActivitiesWithMe->addRelatedActivity($this);
        }

        return $this;
    }

    public function removeRelatedActivitiesWithMe(Activity $relatedActivitiesWithMe): self
    {
        if ($this->relatedActivitiesWithMe->contains($relatedActivitiesWithMe)) {
            $this->relatedActivitiesWithMe->removeElement($relatedActivitiesWithMe);
            $relatedActivitiesWithMe->removeRelatedActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getRelatedActivities(): Collection
    {
        return $this->relatedActivities;
    }

    public function addRelatedActivity(Activity $relatedActivity): self
    {
        if (!$this->relatedActivities->contains($relatedActivity)) {
            $this->relatedActivities[] = $relatedActivity;
        }

        return $this;
    }

    public function removeRelatedActivity(Activity $relatedActivity): self
    {
        if ($this->relatedActivities->contains($relatedActivity)) {
            $this->relatedActivities->removeElement($relatedActivity);
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Activity $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Activity $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}

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
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="activity", cascade={"persist"}, orphanRemoval=true)
     */
    private $photo;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\Award", mappedBy="activities")
     */
    private $awards;    

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", mappedBy="secondaryActivities")
     */
    private $lawyers_secondary;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lawyer", mappedBy="specificActivities")
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
        $this->relatedActivitiesWithMe = new ArrayCollection();
        $this->relatedActivities = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->lawyers_secondary = new ArrayCollection();
        $this->Article = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->insights = new ArrayCollection();
        $this->caseStudies = new ArrayCollection();
        $this->publication = new ArrayCollection();
        $this->quote = new ArrayCollection();
        $this->awards = new ArrayCollection();
        $this->key_contacts = new ArrayCollection();
        
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
            $child->addParent($this);
        }

        return $this;
    }

    public function removeChild(Activity $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            $child->removeParent($this);
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(Activity $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents[] = $parent;
        }

        return $this;
    }

    public function removeParent(Activity $parent): self
    {
        if ($this->parents->contains($parent)) {
            $this->parents->removeElement($parent);
        }

        return $this;
    }

    /**
     * @return Collection|Insight[]
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }

    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->addActivity($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->contains($insight)) {
            $this->insights->removeElement($insight);
            $insight->removeActivity($this);
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
            $caseStudy->addActivity($this);
        }

        return $this;
    }

    public function removeCaseStudy(CaseStudy $caseStudy): self
    {
        if ($this->caseStudies->contains($caseStudy)) {
            $this->caseStudies->removeElement($caseStudy);
            $caseStudy->removeActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublication(): Collection
    {
        return $this->publication;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publication->contains($publication)) {
            $this->publication[] = $publication;
            $publication->addActivity($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->contains($publication)) {
            $this->publication->removeElement($publication);
            $publication->removeActivity($this);
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

    public function getPhoto(): ?Resource
    {
        return $this->photo;
    }

    public function setPhoto(?Resource $photo): self
    {
        $this->photo = $photo;

        // set (or unset) the owning side of the relation if necessary
        $newActivity = null === $photo ? null : $this;
        if ($photo->getActivity() !== $newActivity) {
            $photo->setActivity($newActivity);
        }

        return $this;
    }

    /**
     * @return Collection|Award[]
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $award): self
    {
        if (!$this->awards->contains($award)) {
            $this->awards[] = $award;
            $award->addActivity($this);
        }

        return $this;
    }

    public function removeAward(Award $award): self
    {
        if ($this->awards->contains($award)) {
            $this->awards->removeElement($award);
            $award->removeActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection|Lawyer[]
     */
    public function getKeyContacts(): Collection
    {
        return $this->key_contacts;
    }

    public function addKeyContact(Lawyer $keyContact): self
    {
        if (!$this->key_contacts->contains($keyContact)) {
            $this->key_contacts[] = $keyContact;
            $keyContact->setSpecificActivities($this);
        }

        return $this;
    }

    public function removeKeyContact(Lawyer $keyContact): self
    {
        if ($this->key_contacts->contains($keyContact)) {
            $this->key_contacts->removeElement($keyContact);
            // set the owning side to null (unless already changed)
            if ($keyContact->getSpecificActivities() === $this) {
                $keyContact->setSpecificActivities(null);
            }
        }

        return $this;
    }


}

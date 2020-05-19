<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;


    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $featured;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="article", cascade={"persist"}, orphanRemoval=true)
     */
    private $attachments;

    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ArticleCategory", inversedBy="article")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="Article")
     */
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", inversedBy="Article")
     */
    private $lawyers;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Person", cascade="persist", inversedBy="articles")
     */
    private $people;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Office", inversedBy="Article")
     */
    private $offices;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $publication_date;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->lawyers = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->category = new ArrayCollection();
        $this->people = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFeatured(): ?int
    {
        return $this->featured;
    }

    public function setFeatured(int $featured): self
    {
        $this->featured = $featured;

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
            $attachment->setArticle($this);
        }

        return $this;
    }

    public function removeAttachment(Resource $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getArticle() === $this) {
                $attachment->setArticle(null);
            }
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
    
    /**
     * @return Collection|Office[]
     */
    public function getOffices(): Collection
    {
        return $this->offices;
    }

    public function addOffice(Office $office): self
    {
        if (!$this->offices->contains($office)) {
            $this->offices[] = $office;
        }

        return $this;
    }

    public function removeOffice(Office $office): self
    {
        if ($this->offices->contains($office)) {
            $this->offices->removeElement($office);
        }

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publication_date;
    }

    public function setPublicationDate(\DateTimeInterface $publication_date): self
    {
        $this->publication_date = $publication_date;

        return $this;
    }

    /**
     * @return Collection|ArticleCategory[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(ArticleCategory $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(ArticleCategory $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
        }

        return $this;
    }

}

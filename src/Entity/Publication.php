<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"academy" = "Academy", "news" = "News","opinion" = "Opinion", "legalNovelty" = "LegalNovelty"})
 *
 */
abstract class Publication extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $featured;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $format;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $url_video;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="publication", cascade={"persist"}, orphanRemoval=true)
     */
    private $attachments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="publication")
     */
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Person", cascade="persist", inversedBy="publications")
     */
    private $people;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Office", inversedBy="publication")
     */
    private $offices;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publication_date;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Insight", inversedBy="publications")
     */
    private $insights;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Page", inversedBy="publications")
     */
    private $pages;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Legislation", cascade="persist", inversedBy="publications")
     */
    private $legislations;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $originalTableCode;
    

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->insights = new ArrayCollection();
        $this->legislations = new ArrayCollection();
        $this->publication_date = new \DateTime();
        $this->pages = new ArrayCollection();
    }

    public function getFeatured(): ?int
    {
        return $this->featured;
    }

    public function setFeatured(?int $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getUrlVideo(): ?string
    {
        return $this->url_video;
    }

    public function setUrlVideo(?string $url_video): self
    {
        $this->url_video = $url_video;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publication_date;
    }

    public function setPublicationDate(?\DateTimeInterface $publication_date): self
    {
        $this->publication_date = $publication_date;

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
            $attachment->setPublication($this);
        }

        return $this;
    }

    public function removeAttachment(Resource $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getPublication() === $this) {
                $attachment->setPublication(null);
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
            $insight->addPublication($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->contains($insight)) {
            $this->insights->removeElement($insight);
            $insight->removePublication($this);
        }

        return $this;
    }

    /**
     * @return Collection|Legislation[]
     */
    public function getLegislations(): Collection
    {
        return $this->legislations;
    }

    public function addLegislation(Legislation $legislation): self
    {
        if (!$this->legislations->contains($legislation)) {
            $this->legislations[] = $legislation;
        }

        return $this;
    }

    public function removeLegislation(Legislation $legislation): self
    {
        if ($this->legislations->contains($legislation)) {
            $this->legislations->removeElement($legislation);
        }

        return $this;
    }

    public function getOriginalTableCode(): ?int
    {
        return $this->originalTableCode;
    }

    public function setOriginalTableCode(?int $originalTableCode): self
    {
        $this->originalTableCode = $originalTableCode;

        return $this;
    }

    /**
     * @return Collection|Page[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
        }

        return $this;
    }


    
}

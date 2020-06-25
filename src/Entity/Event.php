<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventType;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $customMap;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $customSignup;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Person", cascade="persist", inversedBy="events")
     */
    private $people;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="events")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resource", mappedBy="event", cascade={"persist"}, orphanRemoval=true)
     */
    private $attachments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Office", inversedBy="event")
     * @ORM\JoinColumn(nullable=true)
     */
    private $office;


    public function __construct()
    {
        $this->people = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getCustomMap(): ?string
    {
        return $this->customMap;
    }

    public function setCustomMap(?string $customMap): self
    {
        $this->customMap = $customMap;

        return $this;
    }

    public function getCustomSignup(): ?string
    {
        return $this->customSignup;
    }

    public function setCustomSignup(?string $customSignup): self
    {
        $this->customSignup = $customSignup;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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
            $attachment->setEvent($this);
        }

        return $this;
    }

    public function removeAttachment(Resource $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getEvent() === $this) {
                $attachment->setEvent(null);
            }
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

    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }
}

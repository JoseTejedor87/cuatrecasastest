<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventsBlockRepository")
 */
class EventsBlock extends Block
{

    public function getBlockType(): ?string
    {
        return 'eventsBlock';
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfEvents;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eventType;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity")
     */
    private $activities;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
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

    public function removeEvent(Event $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

        return $this;
    }

    public function getNumberOfEvents(): ?int
    {
        return $this->numberOfEvents;
    }

    public function setNumberOfEvents(?int $numberOfEvents): self
    {
        $this->numberOfEvents = $numberOfEvents;

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

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

        return $this;
    }
}
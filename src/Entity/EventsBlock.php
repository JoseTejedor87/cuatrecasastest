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
     * @ORM\ManyToMany(targetEntity="App\Entity\Event")
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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

}
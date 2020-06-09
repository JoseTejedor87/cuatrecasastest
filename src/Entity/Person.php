<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person extends Item
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $position;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", mappedBy="people")
     * @ORM\OrderBy({"startDate" = "DESC"})
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", mappedBy="people")
     */
    private $articles;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Lawyer", inversedBy="person" , cascade={"persist"})
     */
    private $lawyer;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFullName(): ?string
    {
        $lawyer = $this->getLawyer();
        if ($lawyer) {
            return $lawyer->getFullName();
        } else {
            return $this->surname . ", " . $this->name;
        }
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

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
            $event->addPeople($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            $event->removePeople($this);
        }

        return $this;
    }

    public function getLawyer(): ?Lawyer
    {
        return $this->lawyer;
    }

    public function setLawyer(?Lawyer $lawyer): self
    {
        $this->lawyer = $lawyer;

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
            $article->addPerson($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            $article->removePerson($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTranslationRepository")
 */
class EventTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $schedule;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $program;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customAddress;


    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(?string $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(?string $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getCustomCity(): ?string
    {
        return $this->customCity;
    }

    public function setCustomCity(?string $customCity): self
    {
        $this->customCity = $customCity;

        return $this;
    }

    public function getCustomAddress(): ?string
    {
        return $this->customAddress;
    }

    public function setCustomAddress(?string $customAddress): self
    {
        $this->customAddress = $customAddress;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }


}
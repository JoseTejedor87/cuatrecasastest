<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTranslationRepository")
 */
class EventTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $place_description;

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPlaceDescription(): ?string
    {
        return $this->place_description;
    }

    public function setPlaceDescription(string $place_description): self
    {
        $this->place_description = $place_description;

        return $this;
    }

}
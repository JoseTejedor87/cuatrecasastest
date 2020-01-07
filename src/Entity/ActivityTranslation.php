<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityTranslationRepository")
 */
class ActivityTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $experience;


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }
}
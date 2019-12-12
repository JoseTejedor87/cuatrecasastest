<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LawyerTranslationRepository")
 */
class LawyerTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $cv;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $experience;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $training;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mentions;



    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTraining(): ?string
    {
        return $this->training;
    }

    public function setTraining(?string $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getMentions(): ?string
    {
        return $this->mentions;
    }

    public function setMentions(?string $mentions): self
    {
        $this->mentions = $mentions;

        return $this;
    }
}

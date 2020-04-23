<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AwardTranslationRepository")
 */
class AwardTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $granted;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_award;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_award_firma;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_award_indiv;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tags;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getGranted(): ?string
    {
        return $this->granted;
    }

    public function setGranted(string $granted): self
    {
        $this->granted = $granted;

        return $this;
    }

    public function getDescAward(): ?string
    {
        return $this->desc_award;
    }

    public function setDescAward(string $desc_award): self
    {
        $this->desc_award = $desc_award;

        return $this;
    }

    public function getDescAwardFirma(): ?string
    {
        return $this->desc_award_firma;
    }

    public function setDescAwardFirma(string $desc_award_firma): self
    {
        $this->desc_award_firma = $desc_award_firma;

        return $this;
    }

    public function getDescAwardIndiv(): ?string
    {
        return $this->desc_award_indiv;
    }

    public function setDescAwardIndiv(string $desc_award_indiv): self
    {
        $this->desc_award_indiv = $desc_award_indiv;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}

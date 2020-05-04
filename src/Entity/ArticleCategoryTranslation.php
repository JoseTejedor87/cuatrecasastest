<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleCategoryTranslationRepository")
 */
class ArticleCategoryTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $oldlink;

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

    public function getOldlink(): ?string
    {
        return $this->oldlink;
    }

    public function setOldlink(string $oldlink): self
    {
        $this->oldlink = $oldlink;

        return $this;
    }
}

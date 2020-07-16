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


}

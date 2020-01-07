<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class PublishableTranslation
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $metaTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $metaDescription;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    
    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
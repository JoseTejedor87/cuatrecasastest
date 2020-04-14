<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticlesTranslationRepository")
 */
class ArticlesTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $summary;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caption;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="url_pdfArticlesTranslation", cascade={"persist"}, orphanRemoval=true)
     */
    private $url_pdf;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url_link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lawyer_tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $office_tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $practice_tags;

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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }
    public function getUrlPdf(): ?Resource
    {
        return $this->url_pdf;
    }

    public function setUrlPdf(?Resource $url_pdf): self
    {
        $this->url_pdf = $url_pdf;
        if ($url_pdf) {
            $url_pdf->setUrl_pdfArticlesTranslation($this);
        }

        return $this;
    }

    public function getUrlLink(): ?string
    {
        return $this->url_link;
    }

    public function setUrlLink(string $url_link): self
    {
        $this->url_link = $url_link;

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

    public function getLawyerTags(): ?string
    {
        return $this->lawyer_tags;
    }

    public function setLawyerTags(string $lawyer_tags): self
    {
        $this->lawyer_tags = $lawyer_tags;

        return $this;
    }

    public function getOfficeTags(): ?string
    {
        return $this->office_tags;
    }

    public function setOfficeTags(string $office_tags): self
    {
        $this->office_tags = $office_tags;

        return $this;
    }

    public function getPracticeTags(): ?string
    {
        return $this->practice_tags;
    }

    public function setPracticeTags(string $practice_tags): self
    {
        $this->practice_tags = $practice_tags;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResourceRepository")
 * @Vich\Uploadable
 */
class Resource extends Publishable
{

     /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * @Vich\UploadableField(mapping="resources", fileNameProperty="fileName")
     * @var File
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="attachments")
     */
    private $event;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Lawyer", inversedBy="photo")
     */
    private $lawyer;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Office", inversedBy="img_office")
     */
    private $office;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Award", inversedBy="image")
     */
    private $award;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Brand", inversedBy="image")
     */
    private $brand;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Slider", inversedBy="image")
     */
    private $slider;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Publication", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $publication;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CaseStudy", inversedBy="image")
     */
    private $caseStudy;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Activity", inversedBy="photo")
     */
    private $activity;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Region", inversedBy="photo")
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Insight", inversedBy="attachments")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $insight;    

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getLawyer(): ?Lawyer
    {
        return $this->lawyer;
    }

    public function setLawyer(?Lawyer $lawyer): self
    {
        $this->lawyer = $lawyer;

        return $this;
    }
    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }
    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): self
    {
        $this->award = $award;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }


    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getCaseStudy(): ?CaseStudy
    {
        return $this->caseStudy;
    }

    public function setCaseStudy(?CaseStudy $caseStudy): self
    {
        $this->caseStudy = $caseStudy;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getSlider(): ?Slider
    {
        return $this->slider;
    }

    public function setSlider(?Slider $slider): self
    {
        $this->slider = $slider;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getInsight(): ?Insight
    {
        return $this->insight;
    }

    public function setInsight(?Insight $insight): self
    {
        $this->insight = $insight;

        return $this;
    }


}

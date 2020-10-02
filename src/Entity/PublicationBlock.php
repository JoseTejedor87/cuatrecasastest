<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PublicationBlockRepository")
 */
class PublicationBlock extends Block
{

    public function getBlockType(): ?string
    {
        return 'publicationBlock';
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfPublication;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicationType;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication")
     */
    private $publication;

    public function __construct()
    {
        $this->publication = new ArrayCollection();
    }

    public function getNumberOfPublication(): ?int
    {
        return $this->numberOfPublication;
    }

    public function setNumberOfPublication(?int $numberOfPublication): self
    {
        $this->numberOfPublication = $numberOfPublication;

        return $this;
    }

    public function getPublicationType(): ?string
    {
        return $this->publicationType;
    }

    public function setPublicationType(string $publicationType): self
    {
        $this->publicationType = $publicationType;

        return $this;
    }

    /**
     * @return Collection|Publication[]
     */
    public function getPublication(): Collection
    {
        return $this->publication;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publication->contains($publication)) {
            $this->publication[] = $publication;
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->contains($publication)) {
            $this->publication->removeElement($publication);
        }

        return $this;
    }



}

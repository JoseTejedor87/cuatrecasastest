<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page extends Publishable
{

    use ORMBehaviors\Translatable\Translatable;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->publication = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customTemplate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="page", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", mappedBy="pages")
     * @ORM\OrderBy({"publication_date" = "DESC"})
     */
    private $publication;

    /**
     * @return Collection|Block[]
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setPage($this);
        }
        return $this;
    }

    public function removeBlock(Block $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            // set the owning side to null (unless already changed)
            if ($block->getPage() === $this) {
                $block->setPage(null);
            }
        }
        return $this;
    }

    public function getCustomTemplate(): ?string
    {
        return $this->customTemplate;
    }

    public function setCustomTemplate(string $customTemplate): self
    {
        $this->customTemplate = $customTemplate;

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
            $publication->addPage($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publication->contains($publication)) {
            $this->publication->removeElement($publication);
            $publication->removePage($this);
        }

        return $this;
    }



}

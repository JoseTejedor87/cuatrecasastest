<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GeneralBlockRepository")
 */
class GeneralBlock extends Publishable
{

    use ORMBehaviors\Translatable\Translatable;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $blockName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $customTemplate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Block", mappedBy="generalBlock", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $blocks;

    public function getBlockName(): ?string
    {
        return $this->blockName;
    }

    public function setBlockName(string $blockName): self
    {
        $this->blockName = $blockName;

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
            $block->setGeneralBlock($this);
        }

        return $this;
    }

    public function removeBlock(Block $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            // set the owning side to null (unless already changed)
            if ($block->getGeneralBlock() === $this) {
                $block->setGeneralBlock(null);
            }
        }

        return $this;
    }

}

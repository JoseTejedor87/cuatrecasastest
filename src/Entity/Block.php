<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"quoteBlock" = "QuoteBlock", "eventsBlock" = "EventsBlock", "publicationBlock" = "PublicationBlock"})
 *
 */
abstract class Block extends Item
{
    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GeneralBlock", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $generalBlock;    

    abstract public function getBlockType(): ?string;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getGeneralBlock(): ?GeneralBlock
    {
        return $this->generalBlock;
    }

    public function setGeneralBlock(?GeneralBlock $generalBlock): self
    {
        $this->generalBlock = $generalBlock;

        return $this;
    }
}

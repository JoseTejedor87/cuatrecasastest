<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"quoteBlock" = "QuoteBlock", "eventsBlock" = "EventsBlock"})
 *
 */
abstract class Block extends Item
{
    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="blocks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $page;

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

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

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
}

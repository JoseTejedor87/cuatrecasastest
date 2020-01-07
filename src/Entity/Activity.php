<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"sector" = "Sector", "practice" = "Practice", "desk" = "Desk", "product" = "Product"})
 *
 */
abstract class Activity extends Publishable
{

    use ORMBehaviors\Translatable\Translatable;
    
    /**
     * @ORM\Column(type="text")
     */
    private $tags;

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}

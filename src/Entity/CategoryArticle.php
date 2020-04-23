<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryArticleRepository")
 */
class CategoryArticle extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    public function getId(): ?int
    {
        return $this->id;
    }
}

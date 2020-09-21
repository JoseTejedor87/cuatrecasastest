<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use App\Entity\PublishableTranslation;


/**
 * @ORM\Entity(repositoryClass="App\Repository\HomeTranslationRepository")
 */
class HomeTranslation extends PublishableTranslation
{
    use ORMBehaviors\Translatable\Translation;



}

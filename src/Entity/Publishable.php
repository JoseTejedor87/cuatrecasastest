<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Publishable extends Item
{
    /**
     * @ORM\Column(type="json")
     */
    private $languages = [];

    /**
     * @ORM\Column(type="json")
     */
    private $regions = [];

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getRegions(): ?array
    {
        return $this->regions;
    }

    public function setRegions(array $regions): self
    {
        $this->regions = $regions;

        return $this;
    }

    /**
     * A publishable instance is published only when
     * the current language and region received in the request
     * exist in the corresponding collections of the instance.
     * languages and regions respectively
     */

    public function isPublished($language, $region)
    {
        $hasLanguageEnabled = in_array(
            $language,
            $this->getLanguages()
        );
        $hasRegionEnabled = in_array(
            $region,
            $this->getRegions()
        );
        return $hasLanguageEnabled && $hasRegionEnabled;
    }
}

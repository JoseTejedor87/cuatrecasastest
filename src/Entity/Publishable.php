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
    private $locations = [];

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getLocations(): ?array
    {
        return $this->locations;
    }

    public function setLocations(array $locations): self
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * A publishable instance is published only when
     * the current language and location received in the request
     * exist in the corresponding collections of the instance.
     * languages and locations respectively
     */

    public function isPublished($language, $location) {

        // An instance is published only if the
        // current language and location identified throw the request
        // exists in the correspondent collections of the instance

        $hasLanguageEnabled = in_array(
            $language,
            $this->getLanguages()
        );
        $hasLocationEnabled = in_array(
            $location,
            $this->getLocations()
        );
        return $hasLanguageEnabled && $hasLocationEnabled;
    }

}

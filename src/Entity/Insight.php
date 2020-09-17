<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

use App\Entity\Publishable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InsightRepository")
 */
class Insight extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $headerType;

    /**
     * @ORM\ManyToMany(targetEntity="Insight", mappedBy="relatedInsights", cascade={"persist"})
     */
    private $relatedInsightsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="Insight", inversedBy="relatedInsightsWithMe", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="insight_insight",
     *     joinColumns={@ORM\JoinColumn(name="insight_source", referencedColumnName="id", onDelete="NO ACTION")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="insight_target", referencedColumnName="id", onDelete="NO ACTION")}
     * )
     */
    private $relatedInsights;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="insights")
     */
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Publication", inversedBy="insights")
     */
    private $publications;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showIntroBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showKnowledgeBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showEventsBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showLegalNoveltiesBlock;

    /**
         * @ORM\Column(type="boolean", nullable=false)
         */
    private $showCaseStudiesBlock;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Lawyer", inversedBy="insights")
     */
    private $lawyers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Home", inversedBy="insights")
     * @ORM\JoinColumn(nullable=true)
     */
    private $home; 


}

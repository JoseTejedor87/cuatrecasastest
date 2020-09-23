<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HomeRepository")
 */
class Home extends Publishable
{
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showSearchBlock;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showRelatedPublicationAndEvents;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showInsight;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Insight", mappedBy="home",  cascade={"persist"} )
     */
    private $insights;  


    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showCarrerBlock;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="home",  cascade={"persist"} )
     */
    private $quotes;  

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showQuoteBlock;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Brand", mappedBy="home",  cascade={"persist"} )
     */
    private $brand;

    public function __construct()
    {
        $this->insights = new ArrayCollection();
        $this->quotes = new ArrayCollection();
        $this->brand = new ArrayCollection();
    }

    public function addCollections($collArray, $home){
        foreach( $collArray as $item){
            $item->setHome($home);
        }
    }

    public function getShowSearchBlock(): ?bool
    {
        return $this->showSearchBlock;
    }

    public function setShowSearchBlock(bool $showSearchBlock): self
    {
        $this->showSearchBlock = $showSearchBlock;

        return $this;
    }

    public function getShowRelatedPublicationAndEvents(): ?bool
    {
        return $this->showRelatedPublicationAndEvents;
    }

    public function setShowRelatedPublicationAndEvents(bool $showRelatedPublicationAndEvents): self
    {
        $this->showRelatedPublicationAndEvents = $showRelatedPublicationAndEvents;

        return $this;
    }

    public function getShowInsight(): ?bool
    {
        return $this->showInsight;
    }

    public function setShowInsight(bool $showInsight): self
    {
        $this->showInsight = $showInsight;

        return $this;
    }

    public function getShowCarrerBlock(): ?bool
    {
        return $this->showCarrerBlock;
    }

    public function setShowCarrerBlock(bool $showCarrerBlock): self
    {
        $this->showCarrerBlock = $showCarrerBlock;

        return $this;
    }

    public function getShowQuoteBlock(): ?bool
    {
        return $this->showQuoteBlock;
    }

    public function setShowQuoteBlock(bool $showQuoteBlock): self
    {
        $this->showQuoteBlock = $showQuoteBlock;

        return $this;
    }

    /**
     * @return Collection|Insight[]
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }

    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->setHome($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->contains($insight)) {
            $this->insights->removeElement($insight);
            // set the owning side to null (unless already changed)
            if ($insight->getHome() === $this) {
                $insight->setHome(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Brand[]
     */
    public function getBrand(): Collection
    {
        return $this->brand;
    }

    public function addBrand(Brand $brand): self
    {
        if (!$this->brand->contains($brand)) {
            $this->brand[] = $brand;
            $brand->setHome($this);
        }

        return $this;
    }

    public function removeBrand(Brand $brand): self
    {
        if ($this->brand->contains($brand)) {
            $this->brand->removeElement($brand);
            // set the owning side to null (unless already changed)
            if ($brand->getHome() === $this) {
                $brand->setHome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Quote[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
            $quote->setHome($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getHome() === $this) {
                $quote->setHome(null);
            }
        }

        return $this;
    }

 

}

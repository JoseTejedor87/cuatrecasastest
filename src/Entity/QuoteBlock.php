<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuoteBlockRepository")
 */
class QuoteBlock extends Block
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quote")
     */
    private $quote;

    public function getBlockType(): ?string
    {
        return 'quoteBlock';
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): self
    {
        $this->quote = $quote;

        return $this;
    }
}

<?php
namespace Alireza\LaraCart;

use Alireza\LaraCart\Models\Quote;
use Illuminate\Support\Collection;

class LaraCart
{
    protected $identifier;
    protected $identifierStorage;

    public function __construct($identifier, $identifierStorage)
    {
        $this->identifier = $identifier;
        $this->identifierStorage = $identifierStorage;
    }

    /**
     * Get quote.
     *
     * @return Quote
     */
    public function getQuote()
    {
        return Quote::firstOrCreate([
            'cart_id'   =>  $this->identifier
        ], []);
    }

    /**
     * Get cart items.
     *
     * @return Collection
     */
    public function getQuoteItems()
    {
        $quote = $this->getQuote();
        return $quote->items;
    }
}

<?php
namespace Alireza\LaraCart;

use Alireza\LaraCart\Exceptions\CartIdentifierIsEmpty;
use Alireza\LaraCart\Models\Quote;
use Alireza\LaraCart\Models\QuoteItem;
use Illuminate\Support\Collection;

class Cart
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
     * @throws CartIdentifierIsEmpty
     */
    public function getQuote()
    {
        if ($this->identifier === null)
            throw new CartIdentifierIsEmpty();

        return Quote::firstOrCreate([
            'id'   =>  $this->identifier
        ], []);
    }

    /**
     * Get cart items.
     *
     * @return Collection
     * @throws CartIdentifierIsEmpty
     */
    public function getQuoteItems()
    {
        $quote = $this->getQuote();
        return $quote->items;
    }

    public function updateQuote(){}

    public function addItem($productId, $productName = null, $productPrice = 0, $productQty = 1, $conditions = [], $attributes = [])
    {
        $quote = $this->getQuote();
        /** @var QuoteItem $quote_item */
        $quote_item = $quote->items()->firstOrCreate([
            'product_id'        =>  $productId
        ], [
            'product_name'      =>  $productName,
            'product_price'     =>  $productPrice,
            'product_qty'       =>  $productQty,
            'attributes'        =>  $attributes,
        ]);

        if (!$quote_item->wasRecentlyCreated)
            $this->updateItem(
                $quote_item->id,
                $quote_item->toArray() + ['product_qty'=>$quote_item->product_qty+$productQty]
            );

        return $this->getCartContent();
    }

    public function updateItem($itemId, Array $attributes){}
    public function removeItem($itemId){}
    public function getItem($itemId){}

    /**
     * Get cart content
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function getCartContent()
    {
        return $this->getQuote()->load(['items.conditions', 'conditions']);
    }

    /**
     * Check if cart is empty.
     *
     * @return bool
     * @throws CartIdentifierIsEmpty
     */
    public function isEmpty()
    {
        return $this->getQuoteItems()->isEmpty();
    }

    public function getTotalQuantity(){}
    public function getSubTotal(){}
    public function getTotal(){}
    public function clear(){}
    public function condition(){}
    public function getConditions(){}
    public function getCondition($conditionId){}
    public function clearCartConditions(){}
    public function removeCartCondition(){}
    public function addItemCondition(){}
    public function removeItemCondition(){}
    public function clearItemConditions(){}
    public function getConditionsByType(){}
    public function removeConditionsByType(){}



    private function reCalculateSubTotalPrice(){}
    private function reCalculateItemSubTotalPrice(){}
    private function reCalculateTotalPrice(){}
    private function reCalculateItemTotalPrice(){}
}

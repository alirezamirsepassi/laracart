<?php
namespace Alireza\LaraCart;

use Alireza\LaraCart\Exceptions\CartIdentifierIsEmpty;
use Alireza\LaraCart\Exceptions\CartItemDoesNotExist;
use Alireza\LaraCart\Models\Quote;
use Alireza\LaraCart\Models\QuoteItem;

class Cart
{
    /**
     * Quote model on the instance.
     * @var Quote $quote
     */
    protected $quote;
    protected $identifier;
    protected $identifierStorage;

    /**
     * Cart constructor.
     *
     * @param $identifier
     * @param $identifierStorage
     */
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

        return $this->quote ? $this->quote : $this->quote = Quote::firstOrCreate([
            'id' => $this->identifier
        ], []);
    }

    /**
     * Get cart items.
     *
     * @return QuoteItem[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws CartIdentifierIsEmpty
     */
    public function getQuoteItems()
    {
        return $this->getQuote()->items;
    }

    /**
     * Update quote.
     *
     * @param $attributes
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function updateQuote($attributes)
    {
        $this->getQuote()->update($attributes);
        return $this->getCartContent();
    }

    /**
     * Add cart item.
     *
     * @param       $productId
     * @param null  $productName
     * @param int   $productPrice
     * @param int   $productQty
     * @param array $attributes
     * @param array $conditions
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function addItem($productId, $productName = null, $productPrice = 0, $productQty = 1, $attributes = [], $conditions = [])
    {
        $quote_item = $this->getQuote()->items()->firstOrCreate([
            'product_id'        =>  $productId
        ], [
            'product_name'      =>  $productName,
            'product_price'     =>  $productPrice,
            'product_qty'       =>  $productQty,
            'attributes'        =>  $attributes,
        ]);

        if (!$quote_item->wasRecentlyCreated) {
            $this->updateItem($quote_item->id, array_merge($quote_item->toArray(), ['product_qty' => $quote_item->product_qty + $productQty]));
        }

        return $this->getCartContent();
    }

    public function addItems(Array $items){}

    /**
     * Update cart item.
     *
     * @param       $itemId
     * @param array $attributes
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function updateItem($itemId, Array $attributes)
    {
        $item = $this->getItem($itemId);
        if ($item)
            $item->update($attributes);

        return $this->getCartContent();
    }

    /**
     * Remove item from cart
     *
     * @param $itemId
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function removeItem($itemId)
    {
        $item = $this->getItem($itemId);
        if ($item)
            $item->delete();

        return $this->getCartContent();
    }

    /**
     * Find item in cart.
     *
     * @param $itemId
     *
     * @return QuoteItem|null
     * @throws CartIdentifierIsEmpty
     */
    public function getItem($itemId)
    {
        return $this->getQuoteItems()->find($itemId);
    }

    /**
     * Get cart content
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function getCartContent()
    {
        return $this->getQuote()->refresh()->loadMissing(['items.conditions', 'conditions']);
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

    /**
     * Get total items quantity.
     *
     * @return int
     * @throws CartIdentifierIsEmpty
     */
    public function getTotalQuantity()
    {
        return $this->getQuoteItems()->sum('product_qty');
    }

    /**
     * Get cart subtotal.
     *
     * @return int
     * @throws CartIdentifierIsEmpty
     */
    public function getSubTotal()
    {
        return $this->getQuote()->subtotal;
    }

    /**
     * Get cart total.
     *
     * @return mixed
     * @throws CartIdentifierIsEmpty
     */
    public function getTotal()
    {
        return $this->getQuote()->total;
    }

    /**
     * Clear cart items and conditions.
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function clear()
    {
        $quote = $this->getQuote();
        $quote->items()->delete();
        $quote->conditions()->delete();

        return $this->getCartContent();
    }

    /**
     * Add condition to cart.
     *
     * @param       $name
     * @param       $type
     * @param       $target
     * @param       $value
     * @param array $attributes
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function addCondition($name, $type, $target, $value, $attributes = [])
    {
        $this->getQuote()->conditions()->create(compact('name', 'type', 'target', 'value', 'attributes'));
        return $this->getCartContent();
    }

    /**
     * Add condition to cart item.
     *
     * @param       $itemId
     * @param       $name
     * @param       $type
     * @param       $target
     * @param       $value
     * @param array $attributes
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function addItemCondition($itemId, $name, $type, $target, $value, $attributes = [])
    {
        $item = $this->getItem($itemId);
        if ($item)
            $item->conditions()->create(compact('name', 'type', 'target', 'value', 'attributes'));

        return $this->getCartContent();
    }

    /**
     * Get cart conditions.
     *
     * @return Models\QuoteCondition[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws CartIdentifierIsEmpty
     */
    public function getConditions()
    {
        return $this->getQuote()->conditions;
    }

    /**
     * Get cart item conditions.
     *
     * @param $itemId
     *
     * @return Models\QuoteItemCondition[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws CartIdentifierIsEmpty
     * @throws CartItemDoesNotExist
     */
    public function getItemConditions($itemId)
    {
        $item = $this->getItem($itemId);
        if ($item)
            return $item->conditions;
        else
            throw new CartItemDoesNotExist();
    }

    /**
     * Get a cart condition by id.
     *
     * @param $conditionId
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws CartIdentifierIsEmpty
     */
    public function getCondition($conditionId)
    {
        return $this->getConditions()->find($conditionId);
    }

    /**
     * Get item condition.
     *
     * @param $itemId
     * @param $conditionId
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     * @throws CartIdentifierIsEmpty
     * @throws CartItemDoesNotExist
     */
    public function getItemCondition($itemId, $conditionId)
    {
        return $this->getItemConditions($itemId)->find($conditionId);
    }

    /**
     * Clear all cart conditions.
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function clearConditions()
    {
        $this->getQuote()->conditions()->delete();
        return $this->getCartContent();
    }

    /**
     * Clear all cart item conditions.
     *
     * @param $itemId
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function clearItemConditions($itemId)
    {
        $item = $this->getItem($itemId);
        if ($item)
            $item->conditions()->delete();

        return $this->getCartContent();
    }

    /**
     * Remove cart condition.
     *
     * @param $conditionId
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     * @throws \Exception
     */
    public function removeCondition($conditionId)
    {
        $condition = $this->getCondition($conditionId);
        if ($condition)
            $condition->delete();

        return $this->getCartContent();
    }

    /**
     * Remove cart item condition.
     *
     * @param $itemId
     * @param $conditionId
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     * @throws CartItemDoesNotExist
     */
    public function removeItemCondition($itemId, $conditionId)
    {
        $condition = $this->getItemCondition($itemId, $conditionId);
        if ($condition)
            $condition->delete();

        return $this->getCartContent();
    }

    /**
     * Get cart conditions by their type.
     * @param $type
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws CartIdentifierIsEmpty
     */
    public function getConditionsByType($type)
    {
        return $this->getConditions()->where('type', $type);
    }

    /**
     * Remove cart conditions by their type.
     *
     * @param $type
     *
     * @return Quote
     * @throws CartIdentifierIsEmpty
     */
    public function removeConditionsByType($type)
    {
        $this->getQuote()->conditions()->where('type', $type)->delete();
        return $this->getCartContent();
    }


    /**
     * Calculates total price.
     *
     * @return int
     * @throws CartIdentifierIsEmpty
     */
    private function reCalculateSubTotalPrice()
    {
        $subTotal = 0;
        $items = $this->getQuoteItems()->each(function ($item) use (&$subTotal) {
            $subTotal += $this->reCalculateItemSubTotalPrice($item);
        });

        return $subTotal;
    }

    /**
     * Calculates subtotal price of item.
     *
     * @param QuoteItem $item
     * @return float|int
     */
    private function reCalculateItemSubTotalPrice(QuoteItem $item)
    {
        return $item->product_qty * $item->price;
    }

    private function reCalculateTotalPrice(){}

    /**
     * Calculate total item price.
     *
     * @param QuoteItem $item
     * @return float|mixed
     */
    private function reCalculateItemTotalPrice(QuoteItem $item)
    {
        $total = $item->subtotal;
        if ($item->conditions->isNotEmpty()) {
            // calculate condition
            // $total += '';
        }

        return $total;
    }
}

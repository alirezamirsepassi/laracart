<?php

namespace Alireza\LaraCart\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'quote_items';
    protected $guarded = ['id'];
    protected $touches = ['quote'];
    protected $fillable = [
        'quote_id', 'product_id', 'product_name', 'product_price',
        'product_qty', 'subtotal', 'total', 'attributes'
    ];
    protected $casts = [
        'attributes'    =>  'array'
    ];
    public $timestamps = true;


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function conditions()
    {
        return $this->hasMany(QuoteItemCondition::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}

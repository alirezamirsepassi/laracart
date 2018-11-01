<?php
namespace Alireza\LaraCart\Facades;

use Illuminate\Support\Facades\Facade;

class LaraCart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}

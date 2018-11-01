<?php
namespace Darryldecode\Cart\Facades;

use Illuminate\Support\Facades\Facade;

class LaraCartFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
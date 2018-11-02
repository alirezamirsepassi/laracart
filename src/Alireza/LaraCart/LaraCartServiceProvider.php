<?php

namespace Alireza\LaraCart;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Alireza\LaraCart\Middleware\CartInitializer;

class LaraCartServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/config/laracart.php', 'laracart');
        $this->app[Kernel::class]->pushMiddleware(CartInitializer::class);

        $this->publishes([
            __DIR__ . '/config/laracart.php' => config_path('laracart.php'),
        ], 'config');
        // $this->publishes([
        //     __DIR__ . '/database/migrations' => database_path('migrations'),
        // ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cart', function ($app) {
            /** @var Application $app */
            $cartIdentifierStorage = config('laracart.identifier_storage');
            $cartIdentifier = $cartIdentifierStorage == 'cookie' ? $app->make('request')->cookie('cart_id', null) : $app->make('session')->get('cart_id', null);

            return new Cart($cartIdentifier, $cartIdentifierStorage);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}

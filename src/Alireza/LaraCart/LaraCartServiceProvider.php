<?php

namespace Alireza\LaraCart;

use Alireza\LaraCart\Middleware\CartInitializer;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class LaraCartServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/Config/laracart.php', 'laraCart');
        $this->app[Kernel::class]->pushMiddleware(CartInitializer::class);

        $this->publishes([
            __DIR__ . '/config/laracart.php' => config_path('laracart.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], 'migrations');
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
            $cartIdentifierStorage = config('laraCart.identifier_storage');
            $cartIdentifier = $app->make($cartIdentifierStorage)->get('cart_id', null);
            return new LaraCart($cartIdentifier, $cartIdentifierStorage);
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

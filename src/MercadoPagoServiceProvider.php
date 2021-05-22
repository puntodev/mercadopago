<?php

namespace Puntodev\MercadoPago;

use Illuminate\Support\ServiceProvider;

class MercadoPagoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mercadopago.php' => config_path('mercadopago.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/mercadopago.php', 'mercadopago');

        // Register the main class to use with the facade
        $this->app->singleton(MercadoPago::class, function ($app) {
            $clientKey = config('mercadopago.client_id');
            $clientSecret = config('mercadopago.client_secret');
            $useSandbox = config('mercadopago.use_sandbox');

            return new MercadoPago(
                $clientKey,
                $clientSecret,
                $useSandbox
            );
        });
        $this->app->alias(MercadoPago::class, 'mercadopago');
    }
}

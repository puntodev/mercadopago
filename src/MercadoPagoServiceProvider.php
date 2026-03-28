<?php

namespace Puntodev\MercadoPago;

use Illuminate\Support\ServiceProvider;

class MercadoPagoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mercadopago.php' => config_path('mercadopago.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mercadopago.php', 'mercadopago');

        $this->app->singleton(MercadoPago::class, function ($app) {
            return new MercadoPagoClient(
                config('mercadopago.use_sandbox', false),
            );
        });
        $this->app->alias(MercadoPago::class, 'mercadopago');
    }
}

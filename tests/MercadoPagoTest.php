<?php

namespace Tests;

use Puntodev\Payments\MercadoPago;
use Puntodev\Payments\MercadoPagoApi;

class MercadoPagoTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mercadoPago.client_id', 'MERCADOPAGO_ID');
        $app['config']->set('mercadoPago.client_secret', 'MERCADOPAGO_SECRET');
        $app['config']->set('mercadoPago.use_sandbox', 'true');
    }

    /** @test */
    public function default_client()
    {
        /** @var MercadoPago $mercadoPago */
        $mercadoPago = $this->app->make('mercadoPago');

        $client = $mercadoPago->defaultClient();

        $this->assertInstanceOf(MercadoPagoApi::class, $client);
    }

    /** @test */
    public function with_credentials()
    {
        /** @var MercadoPago $mercadoPago */
        $mercadoPago = $this->app->make('mercadoPago');

        $client = $mercadoPago->withCredentials('A', 'B');

        $this->assertInstanceOf(MercadoPagoApi::class, $client);
    }
}

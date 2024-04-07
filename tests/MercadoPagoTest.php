<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Puntodev\MercadoPago\MercadoPago;
use Puntodev\MercadoPago\MercadoPagoApi;

class MercadoPagoTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mercadopago.client_id', 'MERCADOPAGO_ID');
        $app['config']->set('mercadopago.client_secret', 'MERCADOPAGO_SECRET');
        $app['config']->set('mercadopago.use_sandbox', 'true');
    }

    #[Test]
    public function default_client()
    {
        /** @var MercadoPago $mercadoPago */
        $mercadoPago = $this->app->make('mercadopago');

        $client = $mercadoPago->defaultClient();

        $this->assertInstanceOf(MercadoPagoApi::class, $client);
    }

    #[Test]
    public function with_credentials()
    {
        /** @var MercadoPago $mercadoPago */
        $mercadoPago = $this->app->make('mercadopago');

        $client = $mercadoPago->withCredentials('A', 'B');

        $this->assertInstanceOf(MercadoPagoApi::class, $client);
    }

    #[Test]
    public function using_sandbox()
    {
        /** @var MercadoPago $mercadoPago */
        $mercadoPago = $this->app->make('mercadopago');

        $usingSandbox = $mercadoPago->usingSandbox();

        $this->assertTrue($usingSandbox);
    }
}

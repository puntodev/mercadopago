<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Puntodev\MercadoPago\MercadoPagoServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MercadoPagoServiceProvider::class
        ];
    }
}

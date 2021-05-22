<?php

namespace Puntodev\MercadoPago\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \Puntodev\MercadoPago\MercadoPagoApi defaultClient()
 * @method static \Puntodev\MercadoPago\MercadoPagoApi withCredentials(string $clientId, string $clientSecret)
 *
 * @see \Puntodev\MercadoPago\MercadoPago
 */
class MercadoPago extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Puntodev\MercadoPago\MercadoPago::class;
    }
}

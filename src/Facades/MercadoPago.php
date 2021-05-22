<?php

namespace Puntodev\Payments\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \Puntodev\Payments\MercadoPagoApi defaultClient()
 * @method static \Puntodev\Payments\MercadoPagoApi withCredentials(string $clientId, string $clientSecret)
 *
 * @see \Puntodev\Payments\MercadoPago
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
        return \Puntodev\Payments\MercadoPago::class;
    }
}

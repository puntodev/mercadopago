<?php

namespace Puntodev\Payments\Facades;

use Illuminate\Support\Facades\Facade;
use Puntodev\Payments\MercadoPago;

class MercadoPagoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MercadoPago::class;
    }
}

<?php

namespace Puntodev\Payments\Facades;

use Illuminate\Support\Facades\Facade;
use Puntodev\Payments\MercadoPago as MercadoPagoClass;

class MercadoPago extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MercadoPagoClass::class;
    }
}

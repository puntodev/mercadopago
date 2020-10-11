<?php

namespace Puntodev\Payments;

use Illuminate\Support\Facades\Facade;

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

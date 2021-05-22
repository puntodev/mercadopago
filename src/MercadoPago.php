<?php

namespace Puntodev\MercadoPago;

class MercadoPago
{
    /**
     * MercadoPago constructor.
     *
     * @param bool $useSandbox
     */
    public function __construct(private bool $useSandbox = false)
    {
    }

    public function defaultClient(): MercadoPagoApi
    {
        $clientId = config('mercadopago.client_id');
        $clientSecret = config('mercadopago.client_secret');

        return new MercadoPagoApi(
            $clientId,
            $clientSecret,
        );
    }

    public function withCredentials(string $clientId, string $clientSecret): MercadoPagoApi
    {
        return new MercadoPagoApi(
            $clientId,
            $clientSecret,
        );
    }

    public function usingSandbox(): bool {
        return $this->useSandbox;
    }
}

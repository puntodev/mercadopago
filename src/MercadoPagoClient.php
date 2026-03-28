<?php

namespace Puntodev\MercadoPago;

class MercadoPagoClient implements MercadoPago
{
    public function __construct(private bool $useSandbox = false)
    {
    }

    public function defaultClient(): MercadoPagoApi
    {
        return new MercadoPagoApiClient(
            config('mercadopago.client_id'),
            config('mercadopago.client_secret'),
        );
    }

    public function withCredentials(string $clientId, string $clientSecret): MercadoPagoApi
    {
        return new MercadoPagoApiClient(
            $clientId,
            $clientSecret,
        );
    }

    public function usingSandbox(): bool
    {
        return $this->useSandbox;
    }
}

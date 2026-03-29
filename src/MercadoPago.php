<?php

namespace Puntodev\MercadoPago;

interface MercadoPago
{
    public function defaultClient(): MercadoPagoApi;

    public function withCredentials(string $clientId, string $clientSecret): MercadoPagoApi;

    public function usingSandbox(): bool;
}

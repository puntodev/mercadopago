<?php

namespace Puntodev\Payments;

class MercadoPago
{
    /** @var string */
    private string $clientId;

    /** @var string */
    private string $clientSecret;

    /** @var bool */
    private bool $useSandbox;

    /**
     * MercadoPagoApiFactory constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param bool $useSandbox
     */
    public function __construct(string $clientId, string $clientSecret, bool $useSandbox)
    {
        $this->useSandbox = $useSandbox;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function defaultClient(): MercadoPagoApi
    {
        return new MercadoPagoApi(
            $this->clientId,
            $this->clientSecret,
            $this->useSandbox
        );
    }

    public function withCredentials($clientId, $clientSecret): MercadoPagoApi
    {
        return new MercadoPagoApi(
            $clientId,
            $clientSecret,
            $this->useSandbox
        );
    }
}

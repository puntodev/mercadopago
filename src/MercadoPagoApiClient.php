<?php

namespace Puntodev\MercadoPago;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoApiClient implements MercadoPagoApi
{
    private string $apiClientKey;
    private string $apiClientSecret;
    private string $host;

    public function __construct(string $apiClientKey, string $apiClientSecret)
    {
        $this->apiClientKey = $apiClientKey;
        $this->apiClientSecret = $apiClientSecret;
        $this->host = 'api.mercadopago.com';
    }

    /**
     * @throws RequestException
     */
    public function createPaymentPreference(array $order): array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->post("https://{$this->host}/checkout/preferences", $order)
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     */
    public function findMerchantOrders(array $query = []): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->withQueryParameters($query)
            ->get("https://{$this->host}/merchant_orders")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     */
    public function findMerchantOrderById(string $id): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->get("https://{$this->host}/merchant_orders/$id")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     */
    public function findPayments(array $query = []): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->withQueryParameters($query)
            ->get("https://{$this->host}/v1/payments/search")
            ->throw()
            ->json();
    }

    /**
     * @throws RequestException
     */
    public function findPaymentById(string $id): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->get("https://{$this->host}/v1/payments/$id")
            ->throw()
            ->json();
    }

    private function getToken(): array
    {
        return Cache::remember("mercadopago-token-{$this->apiClientKey}", 1000, function () {
            Log::debug('Obtaining MercadoPago token from live server');
            return Http::withBasicAuth($this->apiClientKey, $this->apiClientSecret)
                ->asJson()
                ->post("https://{$this->host}/oauth/token", [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->apiClientKey,
                    'client_secret' => $this->apiClientSecret,
                ])
                ->throw()
                ->json();
        });
    }
}

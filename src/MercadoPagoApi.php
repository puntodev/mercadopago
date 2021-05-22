<?php


namespace Puntodev\MercadoPago;


use GuzzleHttp\RequestOptions;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoApi
{
    /** @var string */
    private string $apiClientKey;

    /** @var string */
    private string $apiClientSecret;

    /** @var string */
    private string $host;

    /**
     * MercadoPagoApi constructor.
     * @param string $apiClientKey
     * @param string $apiClientSecret
     */
    public function __construct(string $apiClientKey, string $apiClientSecret)
    {
        $this->apiClientKey = $apiClientKey;
        $this->apiClientSecret = $apiClientSecret;
        $this->host = 'api.mercadopago.com';
    }

    /**
     * @param array $order
     * @return array
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
     * @param array $query
     * @return array|null
     * @throws RequestException
     */
    public function findMerchantOrders(array $query = []): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->withOptions([RequestOptions::QUERY => $query])
            ->get("https://{$this->host}/merchant_orders")
            ->throw()
            ->json();
    }

    /**
     * @param string $id
     * @return array|null
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
     * @param array $query
     * @return array|null
     * @throws RequestException
     */
    public function findPayments(array $query = []): ?array
    {
        $token = $this->getToken();
        return Http::withToken($token['access_token'])
            ->withOptions([RequestOptions::QUERY => $query])
            ->get("https://{$this->host}/v1/payments/search")
            ->throw()
            ->json();
    }

    /**
     * @param string $id
     * @return array|null
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

    /**
     * @return array
     */
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

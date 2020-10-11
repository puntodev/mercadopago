<?php


namespace Puntodev\Payments;


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

    /** @var string */
    private string $ipnUrl;

    /** @var bool */
    private bool $useSandbox;

    /**
     * MercadoPagoApi constructor.
     * @param string $apiClientKey
     * @param string $apiClientSecret
     * @param bool $useSandbox
     */
    public function __construct(string $apiClientKey, string $apiClientSecret, bool $useSandbox)
    {
        $this->apiClientKey = $apiClientKey;
        $this->apiClientSecret = $apiClientSecret;
        $this->host = 'api.mercadopago.com';
        $this->useSandbox = $useSandbox;
    }

    /**
     * @param array $order
     * @return array
     * @throws RequestException
     */
    public function createOrder(array $order): array
    {
        $token = $this->getToken();
        return Http::withOptions([
            RequestOptions::QUERY => [
                'access_token' => $token['access_token']
            ],
        ])
            ->post("https://{$this->host}/checkout/preferences", $order)
            ->throw()
            ->json();
    }

//    /**
//     * @param string $id
//     * @return array|null
//     * @throws RequestException
//     */
//    public function findOrderById(string $id): ?array
//    {
//        $token = $this->getToken();
//        return Http::withToken($token['access_token'])
//            ->get("https://{$this->host}/v2/checkout/orders/$id")
//            ->throw()
//            ->json();
//    }
//
//    /**
//     * @param string $orderId
//     * @return array|null
//     * @throws RequestException
//     */
//    public function captureOrder(string $orderId): ?array
//    {
//        $token = $this->getToken();
//        return Http::withToken($token['access_token'])
//            ->withHeaders([
//                'Prefer' => 'return=representation',
//            ])
//            ->withBody(null, 'application/json')
//            ->post("https://{$this->host}/v2/checkout/orders/$orderId/capture", [])
//            ->throw()
//            ->json();
//    }
//
//    /**
//     * @param string $querystring
//     * @return string
//     * @throws RequestException
//     */
//    public function verifyIpn(string $querystring)
//    {
//        return Http::withHeaders([
//            'User-Agent' => 'PHP-IPN-Verification-Script',
//            'Connection' => 'Close',
//        ])
//            ->withoutRedirecting()
//            ->withBody('cmd=_notify-validate&' . $querystring, 'application/x-www-form-urlencoded')
//            ->post($this->ipnUrl)
//            ->throw()
//            ->body();
//    }

    /**
     * @return array
     */
    private function getToken(): array
    {
        return Cache::remember("mercadoPago-token-{$this->apiClientKey}", 1000, function () {
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

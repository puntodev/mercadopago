<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Puntodev\MercadoPago\MercadoPagoApi;
use Puntodev\MercadoPago\MercadoPagoApiClient;
use Puntodev\MercadoPago\PaymentPreferenceBuilder;

class MercadoPagoApiTest extends TestCase
{
    use WithFaker;

    private MercadoPagoApi $mercadoPagoApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mercadoPagoApi = new MercadoPagoApiClient(
            config('mercadopago.client_id'),
            config('mercadopago.client_secret'),
        );
    }

    /**
     * @throws RequestException
     */
    #[Test]
    public function create_order()
    {
        $order = (new PaymentPreferenceBuilder)
            ->item()
            ->title('My custom product')
            ->unitPrice(23.20)
            ->quantity(1)
            ->currency('ARS')
            ->make()
            ->payerFirstName($this->faker->firstName)
            ->payerLastName($this->faker->lastName)
            ->payerEmail($this->faker->safeEmail)
            ->notificationUrl('https://localhost:8080/mp/ipn/1')
            ->externalId($this->faker->uuid)
            ->successBackUrl('https://localhost:8080/return')
            ->binaryMode(true)
            ->make();

        $createdOrder = $this->mercadoPagoApi->createPaymentPreference($order);

        $this->assertEquals($order['payer']['name'], $createdOrder['payer']['name']);
        $this->assertEquals($order['payer']['surname'], $createdOrder['payer']['surname']);
        $this->assertEquals($order['payer']['email'], $createdOrder['payer']['email']);
        $this->assertEquals($order['items'][0]['title'], $createdOrder['items'][0]['title']);
        $this->assertEquals($order['items'][0]['unit_price'], $createdOrder['items'][0]['unit_price']);
        $this->assertEquals($order['items'][0]['currency'], $createdOrder['items'][0]['currency_id']);
        $this->assertEquals($order['back_urls']['success'], $createdOrder['back_urls']['success']);
        $this->assertEquals($order['back_urls']['pending'], $createdOrder['back_urls']['pending']);
        $this->assertEquals($order['back_urls']['failure'], $createdOrder['back_urls']['failure']);
        $this->assertTrue($createdOrder['binary_mode']);
        $this->assertFalse($createdOrder['expires']);
        $this->assertNull($createdOrder['expiration_date_from']);
        $this->assertNull($createdOrder['expiration_date_to']);
        $this->assertStringStartsWith('https://sandbox.mercadopago.com.ar/checkout/v1/redirect', $createdOrder['sandbox_init_point']);
        $this->assertStringStartsWith('https://www.mercadopago.com.ar/checkout/v1/redirect', $createdOrder['init_point']);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_merchant_orders()
    {
        $merchantOrders = $this->mercadoPagoApi->findMerchantOrders();
        $this->assertIsArray($merchantOrders);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_merchant_order_by_id()
    {
        $merchantOrderId = $this->createMerchantOrder();

        $merchantOrder = $this->mercadoPagoApi->findMerchantOrderById($merchantOrderId);

        $this->assertEquals($merchantOrderId, $merchantOrder['id']);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_order_by_id_invalid()
    {
        $this->expectException(RequestException::class);
        $this->mercadoPagoApi->findMerchantOrderById('invalid-id');
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_payments()
    {
        $payment = $this->mercadoPagoApi->findPayments();
        $this->assertIsArray($payment);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_payments_by_id()
    {
        $payments = $this->mercadoPagoApi->findPayments(['limit' => 1]);
        $this->assertNotEmpty($payments['results'], 'The MercadoPago test account has no payments. Pay a preference with a test buyer to create one.');
        $paymentId = (string) $payments['results'][0]['id'];

        $payment = $this->mercadoPagoApi->findPaymentById($paymentId);

        $this->assertEquals($paymentId, $payment['id']);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function find_payments_by_id_invalid()
    {
        $this->expectException(RequestException::class);
        $this->mercadoPagoApi->findPaymentById('invalid-id');
    }

    /**
     * @throws RequestException
     */
    private function createMerchantOrder(): string
    {
        $preference = $this->mercadoPagoApi->createPaymentPreference((new PaymentPreferenceBuilder)
            ->item()
            ->title('My custom product')
            ->unitPrice(23.20)
            ->quantity(1)
            ->currency('ARS')
            ->make()
            ->externalId($this->faker->uuid)
            ->successBackUrl('https://localhost:8080/return')
            ->make());

        $accessToken = Http::asJson()
            ->post('https://api.mercadopago.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('mercadopago.client_id'),
                'client_secret' => config('mercadopago.client_secret'),
            ])
            ->throw()
            ->json('access_token');

        return (string) Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/merchant_orders', [
                'preference_id' => $preference['id'],
                'external_reference' => $preference['external_reference'],
                'items' => [[
                    'title' => 'My custom product',
                    'unit_price' => 23.20,
                    'quantity' => 1,
                    'currency_id' => 'ARS',
                ]],
            ])
            ->throw()
            ->json('id');
    }
}

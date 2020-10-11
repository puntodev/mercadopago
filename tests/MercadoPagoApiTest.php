<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Puntodev\Payments\MercadoPagoApi;
use Puntodev\Payments\PaymentPreferenceBuilder;

class MercadoPagoApiTest extends TestCase
{
    use WithFaker;

    private MercadoPagoApi $mercadoPagoApi;

    public function setUp(): void
    {
        parent::setUp();
        $this->mercadoPagoApi = new MercadoPagoApi(
            config('mercadopago.client_id'),
            config('mercadopago.client_secret'),
            config('mercadopago.use_sandbox')
        );
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mercadopago.client_id', env('MERCADOPAGO_API_CLIENT_ID'));
        $app['config']->set('mercadopago.client_secret', env('MERCADOPAGO_API_CLIENT_SECRET'));
        $app['config']->set('mercadopago.use_sandbox', env('SANDBOX_GATEWAYS'));
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function testCreateOrder()
    {
        $order = (new PaymentPreferenceBuilder())
            ->description('My custom product')
            ->amount(23)
            ->currency('ARS')
            ->payerFirstName($this->faker->firstName)
            ->payerLastName($this->faker->lastName)
            ->payerEmail($this->faker->safeEmail)
            ->excludedPaymentMethods(['ticket', 'atm', 'prepaid_card'])
            ->notificationUrl('http://localhost:8080/mp/ipn/1')
            ->externalId($this->faker->uuid)
            ->returnUrl('http://localhost:8080/return')
            ->binaryMode(true)
            ->make();

        $createdOrder = $this->mercadoPagoApi->createPaymentPreference($order);
        Log::debug('Created Order: ', ['createdOrder' => $createdOrder]);

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
        $this->assertStringStartsWith('https://sandbox.mercadopago.com.ar/checkout/v1/redirect', $createdOrder['sandbox_init_point']);
        $this->assertStringStartsWith('https://www.mercadopago.com.ar/checkout/v1/redirect', $createdOrder['init_point']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindMerchantOrders()
    {
        $merchantOrders = $this->mercadoPagoApi->findMerchantOrders();
        $this->assertIsArray($merchantOrders);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindMerchantOrderById()
    {
        $payment = $this->mercadoPagoApi->findMerchantOrderById('1129339369');
        $this->assertIsArray($payment);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindOrderByIdInvalid()
    {
        $this->expectException(RequestException::class);
        $this->mercadoPagoApi->findMerchantOrderById('invalid-id');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindPayments()
    {
        $payment = $this->mercadoPagoApi->findPayments();
        $this->assertIsArray($payment);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindPaymentsById()
    {
        $payment = $this->mercadoPagoApi->findPaymentById('5287653537');
        $this->assertIsArray($payment);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindPaymentsByIdInvalid()
    {
        $this->expectException(RequestException::class);
        $this->mercadoPagoApi->findPaymentById('invalid-id');
    }
}

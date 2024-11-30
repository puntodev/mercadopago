<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Puntodev\MercadoPago\MercadoPagoApi;
use Puntodev\MercadoPago\PaymentPreferenceBuilder;

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
        );
    }

    /**
     * @return void
     * @throws RequestException
     */
    public function testCreateOrder()
    {
        $order = (new PaymentPreferenceBuilder())
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

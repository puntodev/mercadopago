<?php

namespace Tests;

use App\Service;
use Carbon\Carbon;
use Puntodev\Payments\PaymentPreferenceBuilder;
use PHPUnit\Framework\TestCase;

class PaymentPreferenceBuilderTest extends TestCase
{

    /** @test */
    public function create_order_with_int_amount()
    {
        $order = (new PaymentPreferenceBuilder())
            ->externalId('31fe5538-8589-437d-8823-3b0574186a5f')
            ->currency('ARS')
            ->amount(23.206)
            ->description('My custom product')
            ->payerFirstName('John')
            ->payerLastName('Lennon')
            ->payerEmail('john@thebeatles.co.uk')
            ->excludedPaymentMethods(['ticket', 'atm', 'prepaid_card'])
            ->returnUrl('http://localhost:8080/return')
            ->notificationUrl('http://localhost:8080/notification')
            ->binaryMode(true)
            ->make();

        $this->assertEquals([
            'items' => [
                [
                    'title' => 'My custom product',
                    'quantity' => 1,
                    'unit_price' => 23.21,
                    'currency' => 'ARS',
                ],
            ],
            'payer' => [
                'name' => 'John',
                'surname' => 'Lennon',
                'email' => 'john@thebeatles.co.uk',
            ],
            'payment_methods' => [
                'excluded_payment_types' => [
                    ['id' => 'ticket'],
                    ['id' => 'atm'],
                    ['id' => 'prepaid_card'],
                ],
            ],
            'notification_url' => 'http://localhost:8080/notification',
            'expires' => true,
            'expiration_date_from' => Carbon::now()->format('Y-m-d\TH:i:s.000P'),
            'expiration_date_to' => Carbon::now()->addDays(1)->format('Y-m-d\TH:i:s.000P'),
            'external_reference' => '31fe5538-8589-437d-8823-3b0574186a5f',
            'back_urls' => [
                'success' => 'http://localhost:8080/return',
                'pending' => 'http://localhost:8080/return',
                'failure' => 'http://localhost:8080/return',
            ],
            'auto_return' => 'all',
            'binary_mode' => true,
        ], $order);
    }
}

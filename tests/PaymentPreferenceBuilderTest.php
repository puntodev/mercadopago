<?php

namespace Tests;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Puntodev\MercadoPago\PaymentPreferenceBuilder;
use PHPUnit\Framework\TestCase;

class PaymentPreferenceBuilderTest extends TestCase
{

    #[Test]
    public function create_order_with_int_amount()
    {
        $order = (new PaymentPreferenceBuilder())
            ->externalId('31fe5538-8589-437d-8823-3b0574186a5f')
            ->item()
            ->title('My custom product')
            ->unitPrice(23.206)
            ->currency('ARS')
            ->make()
            ->payerFirstName('John')
            ->payerLastName('Lennon')
            ->payerEmail('john@thebeatles.co.uk')
            ->excludedPaymentMethods(['ticket', 'atm', 'prepaid_card'])
            ->successBackUrl('https://localhost:8080/success')
            ->pendingBackUrl('https://localhost:8080/pending')
            ->failureBackUrl('https://localhost:8080/failure')
            ->notificationUrl('https://localhost:8080/notification')
            ->expiration(Carbon::parse("2021-05-21T20:45:32"))
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
            'notification_url' => 'https://localhost:8080/notification',
            'expires' => true,
            'expiration_date_from' => Carbon::now()->format('Y-m-d\TH:i:s.000P'),
            'expiration_date_to' => '2021-05-21T20:45:32.000+00:00',
            'external_reference' => '31fe5538-8589-437d-8823-3b0574186a5f',
            'back_urls' => [
                'success' => 'https://localhost:8080/success',
                'pending' => 'https://localhost:8080/pending',
                'failure' => 'https://localhost:8080/failure',
            ],
            'auto_return' => 'all',
            'binary_mode' => true,
        ], $order);
    }
}

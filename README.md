# MercadoPago API Client for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/puntodev/mercadopago.svg?style=flat-square)](https://packagist.org/packages/puntodev/mercadopago)
[![Total Downloads](https://img.shields.io/packagist/dt/puntodev/mercadopago.svg?style=flat-square)](https://packagist.org/packages/puntodev/mercadopago)

A lightweight Laravel package that wraps the [MercadoPago API](https://www.mercadopago.com.ar/developers)
to create Checkout preferences and look up merchant orders and payments. It uses
Laravel's HTTP client under the hood and caches the OAuth2 access token automatically.

## Requirements

- PHP `>=8.4 <9.0`
- Laravel 12 / 13 (`illuminate/support` `^12.53 || ^13.0`)

## Installation

Install via composer:

```bash
composer require puntodev/mercadopago
```

The package auto-registers its service provider and the `MercadoPago` facade via Laravel
package discovery. To publish the config file:

```bash
php artisan vendor:publish --provider="Puntodev\MercadoPago\MercadoPagoServiceProvider" --tag="config"
```

## Configuration

Set the following environment variables:

```dotenv
MERCADOPAGO_API_CLIENT_ID=your-client-id
MERCADOPAGO_API_CLIENT_SECRET=your-client-secret
SANDBOX_GATEWAYS=true   # exposed via usingSandbox()
```

These map to `config/mercadopago.php`:

```php
return [
    'client_id' => env('MERCADOPAGO_API_CLIENT_ID'),
    'client_secret' => env('MERCADOPAGO_API_CLIENT_SECRET'),
    'use_sandbox' => env('SANDBOX_GATEWAYS', false),
];
```

The API host is always `api.mercadopago.com`. MercadoPago distinguishes sandbox from
production through your credentials/test users: when creating a preference the response
includes both an `init_point` (production) and a `sandbox_init_point` (sandbox) checkout
URL. The `use_sandbox` flag is surfaced through `usingSandbox()` so your application can
decide which checkout URL to use.

## Usage

### Resolving the client

Inject the `MercadoPago` contract (or use the `MercadoPago` facade) and obtain a
`MercadoPagoApi` instance. Use `defaultClient()` to use the configured credentials, or
`withCredentials()` to override them at runtime (e.g. for multi-tenant setups):

```php
use Puntodev\MercadoPago\MercadoPago;

public function __construct(private MercadoPago $mercadoPago) {}

// With the credentials from config/mercadopago.php
$api = $this->mercadoPago->defaultClient();

// Or with per-request credentials
$api = $this->mercadoPago->withCredentials($clientId, $clientSecret);
```

### Building a payment preference

`PaymentPreferenceBuilder` produces the payload for the Checkout Preferences API. Items
are added through the nested `PaymentPreferenceItemBuilder` returned by `item()`. The
`pending` / `failure` back URLs default to the success URL, `auto_return` is `all`, and an
expiration window is only sent when provided:

```php
use Puntodev\MercadoPago\PaymentPreferenceBuilder;

$preference = (new PaymentPreferenceBuilder())
    ->item()
        ->title('My custom product')
        ->unitPrice(23.20)
        ->quantity(1)
        ->currency('ARS')      // defaults to ARS
        ->make()
    ->externalId('your-internal-id')
    ->payerFirstName('John')
    ->payerLastName('Doe')
    ->payerEmail('john@example.com')
    ->notificationUrl('https://example.com/mp/ipn')
    ->successBackUrl('https://example.com/return')
    ->binaryMode(true)
    ->make();
```

### Creating a preference and looking up orders/payments

```php
$created = $api->createPaymentPreference($preference);

// Send the buyer to the appropriate checkout URL
$checkoutUrl = $api->usingSandbox()
    ? $created['sandbox_init_point']
    : $created['init_point'];

// Later, look up merchant orders and payments
$orders = $api->findMerchantOrders(['external_reference' => 'your-internal-id']);
$order = $api->findMerchantOrderById($orderId);

$payments = $api->findPayments(['external_reference' => 'your-internal-id']);
$payment = $api->findPaymentById($paymentId);
```

All methods return the decoded JSON response as an `array` (or `null`) and throw
`Illuminate\Http\Client\RequestException` on HTTP errors.

## Testing

```bash
composer test            # runs PHPUnit
composer test-coverage   # generates HTML coverage report
```

> **Note:** the test suite (`tests/MercadoPagoApiTest.php`) makes **real HTTP calls to
> the MercadoPago API**. You must provide valid credentials via
> `MERCADOPAGO_API_CLIENT_ID` and `MERCADOPAGO_API_CLIENT_SECRET`. `phpunit.xml.dist`
> forces `SANDBOX_GATEWAYS=true`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email mariano.goldman@puntodev.com.ar
instead of using the issue tracker.

## Credits

- [Mariano Goldman](https://github.com/puntodev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

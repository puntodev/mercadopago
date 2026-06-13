# AGENTS.md

Guidance for AI agents working in this repository.

## What this project is

`puntodev/mercadopago` is a **Laravel package** (Composer library) that provides a
lightweight client for the **MercadoPago API**: Checkout preferences, merchant orders
and payment search. It is not an application: it is published to Packagist and
consumed from Laravel apps.

- **Namespace:** `Puntodev\MercadoPago\` (PSR-4, mapped to `src/`)
- **PHP:** `>=8.4 <9.0`
- **Main dependency:** `illuminate/support` (`^12.53 || ^13.0`)
- **License:** MIT

## Architecture

The package is built around two interfaces and their implementations, plus a pair of
builders that assemble the Checkout preference payload.

| File | Role |
|------|------|
| `src/MercadoPago.php` | Factory interface. Exposes `defaultClient()`, `withCredentials($id, $secret)` and `usingSandbox()`. |
| `src/MercadoPagoClient.php` | `MercadoPago` implementation. A `readonly` class that holds the `useSandbox` flag and creates `MercadoPagoApiClient` instances from the configured credentials. |
| `src/MercadoPagoApi.php` | HTTP client interface: `createPaymentPreference`, `findMerchantOrders`, `findMerchantOrderById`, `findPayments`, `findPaymentById`. |
| `src/MercadoPagoApiClient.php` | Real implementation against the MercadoPago API (`api.mercadopago.com`) using Laravel's HTTP client (`Http`). Handles the cached OAuth2 token. |
| `src/PaymentPreferenceBuilder.php` | Fluent builder that assembles the Checkout preference array (`items`, `payer`, `back_urls`, `payment_methods`, expiration, `binary_mode`, etc.). |
| `src/PaymentPreferenceItemBuilder.php` | Nested builder for a single preference item (`title`, `quantity`, `unit_price`, `currency`); `make()` returns to the parent `PaymentPreferenceBuilder`. |
| `src/MercadoPagoServiceProvider.php` | Registers `MercadoPago` as a singleton, publishes the config and applies `mergeConfigFrom`. |
| `src/Facades/MercadoPago.php` | `MercadoPago` facade resolving to the `MercadoPago::class` binding. |
| `config/mercadopago.php` | Reads `MERCADOPAGO_API_CLIENT_ID`, `MERCADOPAGO_API_CLIENT_SECRET`, `SANDBOX_GATEWAYS`. |

### Important behavior details

- **Sandbox vs production:** the `useSandbox` flag is read from the `SANDBOX_GATEWAYS`
  env var and exposed via `usingSandbox()`. Unlike host-switching gateways, the API
  host is always `api.mercadopago.com`: MercadoPago distinguishes sandbox via test
  credentials/test users, and `createPaymentPreference` responses include both an
  `init_point` (production) and a `sandbox_init_point` (sandbox) checkout URL.
- **OAuth2 token:** `getToken()` caches the `access_token` for 1000 seconds under the
  key `mercadopago-token-{clientId}` via `Cache::remember`. It uses `withBasicAuth`
  against `/oauth/token` with `grant_type=client_credentials`.
- **Requests:** all calls use `->throw()`, so HTTP errors propagate as
  `RequestException`. Search endpoints (`findMerchantOrders`, `findPayments`) accept an
  optional query array passed through `withQueryParameters`.
- **PaymentPreferenceBuilder:** `auto_return` is fixed to `all`; the `pending`/`failure`
  back URLs fall back to the success URL; the expiration block is only populated when an
  expiration is set (formatted with `MP_DATE_TIME_FORMAT`); `excluded_payment_types` is
  only sent when excluded methods are provided. Item `unit_price` is rounded to two
  decimals and the default item currency is `ARS`.

## Laravel auto-registration (package discovery)

Defined in `composer.json` → `extra.laravel`:
- Provider: `Puntodev\MercadoPago\MercadoPagoServiceProvider`
- Alias/Facade: `MercadoPago` → `Puntodev\MercadoPago\Facades\MercadoPago`

## How to run and test

```bash
composer install
composer test            # vendor/bin/phpunit
composer test-coverage   # generates HTML coverage report under ./coverage
composer lint            # vendor/bin/pint --test (style check, no changes)
composer format          # vendor/bin/pint (fix style)
```

- Tests use **Orchestra Testbench** (`tests/TestCase.php` extends
  `Orchestra\Testbench\TestCase` and registers the service provider).
- `phpunit.xml.dist` forces `SANDBOX_GATEWAYS=true`.
- ⚠️ **`tests/MercadoPagoApiTest.php` makes real HTTP calls to MercadoPago.** It requires
  valid credentials in `MERCADOPAGO_API_CLIENT_ID` / `MERCADOPAGO_API_CLIENT_SECRET`
  (in `.env` locally, or GitHub Secrets in CI). These are not isolated unit tests.
- CI: `.github/workflows/php.yml` runs on PHP 8.4 on every push/PR to `master`,
  including a Pint code-style check.

## Conventions

- Code style is enforced by **Laravel Pint** (`pint.json`, `laravel` preset). Run
  `composer format` before committing; `composer lint` is what CI runs.
- The interfaces (`MercadoPago`, `MercadoPagoApi`) are the public contract: when adding a
  method to the client, add it to the interface and to the tests as well.
- API methods return `array`/`?array` (Laravel's `->json()`); keep that convention.

## Workflow rules (inherited from the user's global config)

- **Do not commit on `master`.** Always work on a branch or worktree.
- PRs are always opened as **Draft**.
- Run `git pull` before starting to make sure you have the latest version.

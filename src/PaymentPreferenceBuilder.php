<?php

namespace Puntodev\MercadoPago;

use Carbon\Carbon;
use DateTime;

class PaymentPreferenceBuilder
{
    const MP_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s.000P';

    private array $items = [];

    private string $externalId = '';

    private string $payerFirstName = '';

    private string $payerLastName = '';

    private string $payerEmail = '';

    private string $successBackUrl = '';

    private string $pendingBackUrl = '';

    private string $failureBackUrl = '';

    private string $notificationUrl = '';

    private ?DateTime $expiration = null;

    private bool $binaryMode = false;

    private array $excludedPaymentMethods = [];

    /**
     * PaymentPreferenceBuilder constructor.
     */
    public function __construct() {}

    public function externalId(string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function item(): PaymentPreferenceItemBuilder
    {
        return new PaymentPreferenceItemBuilder($this);
    }

    public function payerFirstName(string $payerFirstName): static
    {
        $this->payerFirstName = $payerFirstName;

        return $this;
    }

    public function payerLastName(string $payerLastName): static
    {
        $this->payerLastName = $payerLastName;

        return $this;
    }

    public function payerEmail(string $payerEmail): static
    {
        $this->payerEmail = $payerEmail;

        return $this;
    }

    public function excludedPaymentMethods(array $excludedPaymentMethods): static
    {
        $this->excludedPaymentMethods = $excludedPaymentMethods;

        return $this;
    }

    public function successBackUrl(string $successBackUrl): static
    {
        $this->successBackUrl = $successBackUrl;

        return $this;
    }

    public function pendingBackUrl(string $pendingBackUrl): static
    {
        $this->pendingBackUrl = $pendingBackUrl;

        return $this;
    }

    public function failureBackUrl(string $failureBackUrl): static
    {
        $this->failureBackUrl = $failureBackUrl;

        return $this;
    }

    public function notificationUrl(string $notificationUrl): static
    {
        $this->notificationUrl = $notificationUrl;

        return $this;
    }

    public function expiration(?DateTime $expiration): static
    {
        $this->expiration = $expiration;

        return $this;
    }

    public function binaryMode(bool $binaryMode): static
    {
        $this->binaryMode = $binaryMode;

        return $this;
    }

    public function addItem(array $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    public function make(): array
    {
        $paymentMethods = count($this->excludedPaymentMethods) > 0 ? [
            'excluded_payment_types' => collect($this->excludedPaymentMethods)
                ->map(fn ($pm) => ['id' => $pm])
                ->toArray(),
        ] : [];

        $expiration = [
            'expires' => $this->expiration !== null,
        ];
        if ($this->expiration !== null) {
            $expiration['expiration_date_from'] = Carbon::now()->format(self::MP_DATE_TIME_FORMAT);
            $expiration['expiration_date_to'] = $this->expiration->format(self::MP_DATE_TIME_FORMAT);
        }

        return array_merge([
            'items' => $this->items,
            'payer' => [
                'name' => $this->payerFirstName,
                'surname' => $this->payerLastName,
                'email' => $this->payerEmail,
            ],
            'payment_methods' => $paymentMethods,
            'notification_url' => $this->notificationUrl,
            'external_reference' => $this->externalId,
            'back_urls' => [
                'success' => $this->successBackUrl,
                'pending' => $this->pendingBackUrl ?? $this->successBackUrl,
                'failure' => $this->failureBackUrl ?? $this->successBackUrl,
            ],
            'auto_return' => 'all',
            'binary_mode' => $this->binaryMode,
        ], $expiration);
    }
}

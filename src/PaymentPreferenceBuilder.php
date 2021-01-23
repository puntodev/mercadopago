<?php


namespace Puntodev\Payments;


use Carbon\Carbon;

class PaymentPreferenceBuilder
{
    const MP_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s.000P';

    private string $externalId = '';
    private string $currency = 'ARS';
    private float $amount = 0;
    private string $description = '';
    private string $payerFirstName = '';
    private string $payerLastName = '';
    private string $payerEmail = '';
    private string $returnUrl = '';
    private string $notificationUrl = '';
    private bool $binaryMode = false;
    private array $excludedPaymentMethods = [];

    /**
     * OrderBuilder constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $externalId
     * @return PaymentPreferenceBuilder
     */
    public function externalId(string $externalId): PaymentPreferenceBuilder
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function currency(string $currency): PaymentPreferenceBuilder
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param float $amount
     * @return PaymentPreferenceBuilder
     */
    public function amount(float $amount): PaymentPreferenceBuilder
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $description
     * @return PaymentPreferenceBuilder
     */
    public function description(string $description): PaymentPreferenceBuilder
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $payerFirstName
     * @return PaymentPreferenceBuilder
     */
    public function payerFirstName(string $payerFirstName): PaymentPreferenceBuilder
    {
        $this->payerFirstName = $payerFirstName;
        return $this;
    }

    /**
     * @param string $payerLastName
     * @return PaymentPreferenceBuilder
     */
    public function payerLastName(string $payerLastName): PaymentPreferenceBuilder
    {
        $this->payerLastName = $payerLastName;
        return $this;
    }

    /**
     * @param string $payerEmail
     * @return PaymentPreferenceBuilder
     */
    public function payerEmail(string $payerEmail): PaymentPreferenceBuilder
    {
        $this->payerEmail = $payerEmail;
        return $this;
    }

    /**
     * @param array $excludedPaymentMethods
     * @return PaymentPreferenceBuilder
     */
    public function excludedPaymentMethods(array $excludedPaymentMethods): PaymentPreferenceBuilder
    {
        $this->excludedPaymentMethods = $excludedPaymentMethods;
        return $this;
    }

    /**
     * @param string $returnUrl
     * @return PaymentPreferenceBuilder
     */
    public function returnUrl(string $returnUrl): PaymentPreferenceBuilder
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @param string $notificationUrl
     * @return PaymentPreferenceBuilder
     */
    public function notificationUrl(string $notificationUrl): PaymentPreferenceBuilder
    {
        $this->notificationUrl = $notificationUrl;
        return $this;
    }

    /**
     * @param bool $binaryMode
     * @return PaymentPreferenceBuilder
     */
    public function binaryMode(bool $binaryMode): PaymentPreferenceBuilder
    {
        $this->binaryMode = $binaryMode;
        return $this;
    }

    public function make(): array
    {
        $paymentMethods = count($this->excludedPaymentMethods) > 0 ? [
            'excluded_payment_types' => collect($this->excludedPaymentMethods)
                ->map(fn($pm) => ['id' => $pm])
                ->toArray(),
        ] : [];

        return [
            'items' => [
                [
                    'title' => $this->description,
                    'quantity' => 1,
                    'unit_price' => round($this->amount, 2),
                    'currency' => $this->currency,
                ]
            ],
            'payer' => [
                'name' => $this->payerFirstName,
                'surname' => $this->payerLastName,
                'email' => $this->payerEmail,
            ],
            'payment_methods' => $paymentMethods,
            'notification_url' => $this->notificationUrl,
            'expires' => true,
            'expiration_date_from' => Carbon::now()->format(self::MP_DATE_TIME_FORMAT),
            'expiration_date_to' => Carbon::now()->addDays(1)->format(self::MP_DATE_TIME_FORMAT),
            'external_reference' => $this->externalId,
            'back_urls' => [
                'success' => $this->returnUrl,
                'pending' => $this->returnUrl,
                'failure' => $this->returnUrl,
            ],
            'auto_return' => 'all',
            'binary_mode' => $this->binaryMode,
        ];
    }
}

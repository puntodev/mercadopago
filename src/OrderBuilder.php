<?php


namespace Puntodev\Payments;


use Carbon\Carbon;

class OrderBuilder
{
    const MP_DATE_TIME_FORMAT = 'Y-m-d\TH:i:s.000P';

    private string $externalId = '';
    private string $currency = 'ARS';
    private int $amount = 0;
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
     * @return OrderBuilder
     */
    public function externalId(string $externalId): OrderBuilder
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function currency(string $currency): OrderBuilder
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @param int $amount
     * @return OrderBuilder
     */
    public function amount(int $amount): OrderBuilder
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $description
     * @return OrderBuilder
     */
    public function description(string $description): OrderBuilder
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $payerFirstName
     * @return OrderBuilder
     */
    public function payerFirstName(string $payerFirstName): OrderBuilder
    {
        $this->payerFirstName = $payerFirstName;
        return $this;
    }

    /**
     * @param string $payerLastName
     * @return OrderBuilder
     */
    public function payerLastName(string $payerLastName): OrderBuilder
    {
        $this->payerLastName = $payerLastName;
        return $this;
    }

    /**
     * @param string $payerEmail
     * @return OrderBuilder
     */
    public function payerEmail(string $payerEmail): OrderBuilder
    {
        $this->payerEmail = $payerEmail;
        return $this;
    }

    /**
     * @param array $excludedPaymentMethods
     * @return OrderBuilder
     */
    public function excludedPaymentMethods(array $excludedPaymentMethods): OrderBuilder
    {
        $this->excludedPaymentMethods = $excludedPaymentMethods;
        return $this;
    }

    /**
     * @param string $returnUrl
     * @return OrderBuilder
     */
    public function returnUrl(string $returnUrl): OrderBuilder
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    /**
     * @param string $notificationUrl
     * @return OrderBuilder
     */
    public function notificationUrl(string $notificationUrl): OrderBuilder
    {
        $this->notificationUrl = $notificationUrl;
        return $this;
    }

    /**
     * @param bool $binaryMode
     * @return OrderBuilder
     */
    public function binaryMode(bool $binaryMode): OrderBuilder
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
                    'unit_price' => $this->amount,
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

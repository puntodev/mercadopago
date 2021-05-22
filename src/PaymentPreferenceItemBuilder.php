<?php


namespace Puntodev\Payments;


class PaymentPreferenceItemBuilder
{
    private string $title = '';
    private int $quantity = 1;
    private float $unitPrice = 0.0;
    private string $currency = 'ARS';

    /**
     * PaymentPreferenceItemBuilder constructor.
     */
    public function __construct(private PaymentPreferenceBuilder $paymentPreferenceBuilder)
    {
    }

    /**
     * @param string $title
     * @return PaymentPreferenceItemBuilder
     */
    public function title(string $title): PaymentPreferenceItemBuilder
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param int $quantity
     * @return PaymentPreferenceItemBuilder
     */
    public function quantity(int $quantity): PaymentPreferenceItemBuilder
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param float $unitPrice
     * @return PaymentPreferenceItemBuilder
     */
    public function unitPrice(float $unitPrice): PaymentPreferenceItemBuilder
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @param string $currency
     * @return PaymentPreferenceItemBuilder
     */
    public function currency(string $currency): PaymentPreferenceItemBuilder
    {
        $this->currency = $currency;
        return $this;
    }

    public function make(): PaymentPreferenceBuilder
    {
        $this->paymentPreferenceBuilder->addItem(
            [
                'title' => $this->title,
                'quantity' => $this->quantity,
                'unit_price' => round($this->unitPrice, 2),
                'currency' => $this->currency,
            ]
        );
        return $this->paymentPreferenceBuilder;
    }
}

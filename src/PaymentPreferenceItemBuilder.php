<?php


namespace Puntodev\MercadoPago;


class PaymentPreferenceItemBuilder
{
    private string $title = '';
    private int $quantity = 1;
    private float $unitPrice = 0.0;
    private string $currency = 'ARS';

    /**
     * PaymentPreferenceItemBuilder constructor.
     */
    public function __construct(private readonly PaymentPreferenceBuilder $paymentPreferenceBuilder)
    {
    }

    /**
     * @param string $title
     * @return static
     */
    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param int $quantity
     * @return static
     */
    public function quantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param float $unitPrice
     * @return static
     */
    public function unitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @param string $currency
     * @return static
     */
    public function currency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function make(): PaymentPreferenceBuilder
    {
        return $this->paymentPreferenceBuilder->addItem(
            [
                'title' => $this->title,
                'quantity' => $this->quantity,
                'unit_price' => round($this->unitPrice, 2),
                'currency' => $this->currency,
            ]
        );
    }
}

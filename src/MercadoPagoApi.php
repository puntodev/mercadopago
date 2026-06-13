<?php

namespace Puntodev\MercadoPago;

use Illuminate\Http\Client\RequestException;

interface MercadoPagoApi
{
    /**
     * @throws RequestException
     */
    public function createPaymentPreference(array $order): array;

    /**
     * @throws RequestException
     */
    public function findMerchantOrders(array $query = []): ?array;

    /**
     * @throws RequestException
     */
    public function findMerchantOrderById(string $id): ?array;

    /**
     * @throws RequestException
     */
    public function findPayments(array $query = []): ?array;

    /**
     * @throws RequestException
     */
    public function findPaymentById(string $id): ?array;
}

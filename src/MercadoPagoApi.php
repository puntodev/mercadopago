<?php

namespace Puntodev\MercadoPago;

use Illuminate\Http\Client\RequestException;

interface MercadoPagoApi
{
    /**
     * @param array $order
     * @return array
     * @throws RequestException
     */
    public function createPaymentPreference(array $order): array;

    /**
     * @param array $query
     * @return array|null
     * @throws RequestException
     */
    public function findMerchantOrders(array $query = []): ?array;

    /**
     * @param string $id
     * @return array|null
     * @throws RequestException
     */
    public function findMerchantOrderById(string $id): ?array;

    /**
     * @param array $query
     * @return array|null
     * @throws RequestException
     */
    public function findPayments(array $query = []): ?array;

    /**
     * @param string $id
     * @return array|null
     * @throws RequestException
     */
    public function findPaymentById(string $id): ?array;
}

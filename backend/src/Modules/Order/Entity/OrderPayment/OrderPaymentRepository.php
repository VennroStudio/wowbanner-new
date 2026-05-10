<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderPayment;

interface OrderPaymentRepository
{
    public function getById(int $id): OrderPayment;

    public function findById(int $id): ?OrderPayment;

    /**
     * @return list<OrderPayment>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderPayment $orderPayment): void;

    public function remove(OrderPayment $orderPayment): void;
}

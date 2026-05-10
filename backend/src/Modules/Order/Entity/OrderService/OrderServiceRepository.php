<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderService;

interface OrderServiceRepository
{
    public function getById(int $id): OrderService;

    public function findById(int $id): ?OrderService;

    /**
     * @return list<OrderService>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderService $orderService): void;

    public function remove(OrderService $orderService): void;
}

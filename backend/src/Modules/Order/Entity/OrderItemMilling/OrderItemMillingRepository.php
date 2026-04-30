<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemMilling;

interface OrderItemMillingRepository
{
    public function getById(int $id): OrderItemMilling;

    public function findById(int $id): ?OrderItemMilling;

    /**
     * @return list<OrderItemMilling>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderItemMilling $orderItemMilling): void;

    public function remove(OrderItemMilling $orderItemMilling): void;
}

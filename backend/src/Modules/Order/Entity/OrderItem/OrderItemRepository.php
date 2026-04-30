<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItem;

interface OrderItemRepository
{
    public function getById(int $id): OrderItem;

    public function findById(int $id): ?OrderItem;

    /**
     * @return list<OrderItem>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderItem $orderItem): void;

    public function remove(OrderItem $orderItem): void;
}

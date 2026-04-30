<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemProcessing;

interface OrderItemProcessingRepository
{
    public function getById(int $id): OrderItemProcessing;

    public function findById(int $id): ?OrderItemProcessing;

    /**
     * @return list<OrderItemProcessing>
     */
    public function findByOrderItemId(int $orderItemId): array;

    public function add(OrderItemProcessing $orderItemProcessing): void;

    public function remove(OrderItemProcessing $orderItemProcessing): void;
}

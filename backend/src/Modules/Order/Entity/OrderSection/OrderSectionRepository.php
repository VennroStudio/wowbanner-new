<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderSection;

interface OrderSectionRepository
{
    public function getById(int $id): OrderSection;

    public function findById(int $id): ?OrderSection;

    /**
     * @return list<OrderSection>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderSection $orderSection): void;

    public function remove(OrderSection $orderSection): void;
}

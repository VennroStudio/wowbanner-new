<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderNotification;

interface OrderNotificationRepository
{
    public function getById(int $id): OrderNotification;

    public function findById(int $id): ?OrderNotification;

    /**
     * @return list<OrderNotification>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderNotification $orderNotification): void;

    public function remove(OrderNotification $orderNotification): void;
}

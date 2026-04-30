<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderDelivery;

interface OrderDeliveryRepository
{
    public function getById(int $id): OrderDelivery;

    public function findById(int $id): ?OrderDelivery;

    public function findByOrderId(int $orderId): ?OrderDelivery;

    public function add(OrderDelivery $orderDelivery): void;

    public function remove(OrderDelivery $orderDelivery): void;
}

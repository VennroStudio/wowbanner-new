<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderFile;

interface OrderFileRepository
{
    public function getById(int $id): OrderFile;

    public function findById(int $id): ?OrderFile;

    /**
     * @return list<OrderFile>
     */
    public function findByOrderId(int $orderId): array;

    public function add(OrderFile $orderFile): void;

    public function remove(OrderFile $orderFile): void;
}

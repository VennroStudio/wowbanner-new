<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

interface OrderRepository
{
    public function add(Order $order): void;

    public function getById(int $id): Order;

    public function findById(int $id): ?Order;
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemMilling\FindByOrderId;

final readonly class OrderItemMillingFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

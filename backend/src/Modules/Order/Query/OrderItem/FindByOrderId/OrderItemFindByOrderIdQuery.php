<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItem\FindByOrderId;

final readonly class OrderItemFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

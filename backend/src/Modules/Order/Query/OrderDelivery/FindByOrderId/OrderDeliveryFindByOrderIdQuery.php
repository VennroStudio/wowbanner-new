<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderDelivery\FindByOrderId;

final readonly class OrderDeliveryFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderNotification\FindByOrderId;

final readonly class OrderNotificationFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

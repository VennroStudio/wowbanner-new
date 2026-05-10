<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderSection\FindByOrderId;

final readonly class OrderSectionFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

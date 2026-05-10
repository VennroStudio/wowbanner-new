<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderFile\FindByOrderId;

final readonly class OrderFileFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

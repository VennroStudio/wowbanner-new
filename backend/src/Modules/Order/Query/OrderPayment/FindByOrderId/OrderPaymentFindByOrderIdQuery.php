<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderPayment\FindByOrderId;

final readonly class OrderPaymentFindByOrderIdQuery
{
    public function __construct(
        public int $orderId,
    ) {}
}

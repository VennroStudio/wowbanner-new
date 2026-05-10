<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Create;

final readonly class CreateOrderDeliveryCommand
{
    public function __construct(
        public int $orderId,
        public int $deliveryType,
        public ?string $address = null,
        public ?string $comment = null,
    ) {}
}

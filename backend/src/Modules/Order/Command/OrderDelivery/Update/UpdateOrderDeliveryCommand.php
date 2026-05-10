<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Update;

final readonly class UpdateOrderDeliveryCommand
{
    public function __construct(
        public int $id,
        public int $deliveryType,
        public ?string $address = null,
        public ?string $comment = null,
    ) {}
}

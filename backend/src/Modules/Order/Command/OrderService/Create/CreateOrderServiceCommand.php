<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Create;

final readonly class CreateOrderServiceCommand
{
    public function __construct(
        public int $orderId,
        public int $serviceType,
        public string $price,
        public ?string $note = null,
    ) {}
}

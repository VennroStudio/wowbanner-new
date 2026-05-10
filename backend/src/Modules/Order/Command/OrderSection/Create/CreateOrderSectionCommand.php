<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Create;

final readonly class CreateOrderSectionCommand
{
    public function __construct(
        public int $orderId,
        public int $sectionType,
    ) {}
}

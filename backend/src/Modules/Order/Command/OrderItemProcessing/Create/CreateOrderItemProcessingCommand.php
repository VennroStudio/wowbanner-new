<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Create;

final readonly class CreateOrderItemProcessingCommand
{
    public function __construct(
        public int $orderItemId,
        public int $processingId,
    ) {}
}

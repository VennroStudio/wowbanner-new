<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Update;

final readonly class UpdateOrderItemProcessingCommand
{
    public function __construct(
        public int $id,
        public int $processingId,
    ) {}
}

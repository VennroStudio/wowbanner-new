<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Delete;

final readonly class DeleteOrderItemProcessingCommand
{
    public function __construct(
        public int $id,
    ) {}
}

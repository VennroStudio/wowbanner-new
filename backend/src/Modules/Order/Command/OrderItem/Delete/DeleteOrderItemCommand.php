<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItem\Delete;

final readonly class DeleteOrderItemCommand
{
    public function __construct(
        public int $id,
    ) {}
}

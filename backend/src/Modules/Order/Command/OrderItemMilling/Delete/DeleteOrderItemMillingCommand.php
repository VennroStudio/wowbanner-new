<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemMilling\Delete;

final readonly class DeleteOrderItemMillingCommand
{
    public function __construct(
        public int $id,
    ) {}
}

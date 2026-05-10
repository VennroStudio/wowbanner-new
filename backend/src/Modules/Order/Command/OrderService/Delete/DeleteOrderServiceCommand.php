<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Delete;

final readonly class DeleteOrderServiceCommand
{
    public function __construct(
        public int $id,
    ) {}
}

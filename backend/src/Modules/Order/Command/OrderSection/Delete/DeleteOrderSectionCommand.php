<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderSection\Delete;

final readonly class DeleteOrderSectionCommand
{
    public function __construct(
        public int $id,
    ) {}
}

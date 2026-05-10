<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderDelivery\Delete;

final readonly class DeleteOrderDeliveryCommand
{
    public function __construct(
        public int $id,
    ) {}
}

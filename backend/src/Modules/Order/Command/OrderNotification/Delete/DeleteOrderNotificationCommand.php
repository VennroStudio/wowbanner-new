<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderNotification\Delete;

final readonly class DeleteOrderNotificationCommand
{
    public function __construct(
        public int $id,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderNotification\Create;

final readonly class CreateOrderNotificationCommand
{
    public function __construct(
        public int $orderId,
        public int $notificationType,
    ) {}
}

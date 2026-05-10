<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderNotification\Interface;

interface OrderNotificationModelInterface
{
    public function getId(): int;
    public function toArray(): array;
}

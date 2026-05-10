<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderFile\Fields\Enums;

enum OrderFileDirectory
{
    case FILES;

    public function getPath(int $orderId): string
    {
        return match ($this) {
            self::FILES => "orders/{$orderId}/files",
        };
    }
}

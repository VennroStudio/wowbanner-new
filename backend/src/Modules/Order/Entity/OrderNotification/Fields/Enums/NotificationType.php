<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderNotification\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum NotificationType: int implements EnumInterface
{
    case SENT_TO_PRODUCTION = 1;
    case DELAYED = 2;
    case READY = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::SENT_TO_PRODUCTION => 'Отправлен на производство',
            self::DELAYED            => 'Задерживается',
            self::READY              => 'Готов',
        };
    }
}

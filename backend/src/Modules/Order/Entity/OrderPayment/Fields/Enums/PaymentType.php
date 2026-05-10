<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderPayment\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum PaymentType: int implements EnumInterface
{
    case CASH = 1;
    case CASHLESS = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::CASH => 'Наличный',
            self::CASHLESS => 'Безналичный',
        };
    }
}

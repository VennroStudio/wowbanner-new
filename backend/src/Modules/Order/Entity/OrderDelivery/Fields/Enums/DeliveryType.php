<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderDelivery\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum DeliveryType: int implements EnumInterface
{
    case COURIER = 1;
    case TRANSPORT = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::COURIER => 'Курьер',
            self::TRANSPORT => 'Транспорт',
        };
    }
}

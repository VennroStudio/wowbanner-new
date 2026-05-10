<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderService\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum ServiceType: int implements EnumInterface
{
    case INSTALLATION = 1;
    case DELIVERY = 2;
    case PRINT = 3;
    case LAYOUT = 4;
    case ADDITIONAL = 5;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::INSTALLATION => 'Монтаж',
            self::DELIVERY => 'Доставка',
            self::PRINT => 'Печать',
            self::LAYOUT => 'Макет',
            self::ADDITIONAL => 'Доп.услуги',
        };
    }
}

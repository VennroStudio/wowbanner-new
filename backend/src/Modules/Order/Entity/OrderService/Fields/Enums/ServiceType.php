<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderService\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum ServiceType: int implements EnumInterface
{
    case PRINT = 1;
    case INSTALLATION = 2;
    case LAYOUT = 3;
    case ADDITIONAL = 4;
    case DELIVERY = 5;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::PRINT => 'Печать',
            self::INSTALLATION => 'Монтаж',
            self::LAYOUT => 'Макет',
            self::ADDITIONAL => 'Доп.услуги',
            self::DELIVERY => 'Доставка',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialOption\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum MaterialOptionPricingType: int implements EnumInterface
{
    case BY_AREA = 1;
    case BY_PIECE = 2;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::BY_AREA  => 'По площади',
            self::BY_PIECE => 'Поштучно',
        };
    }
}

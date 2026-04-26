<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingCut\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum MaterialPricingCutType: int implements EnumInterface
{
    case KNOWN = 1;
    case UNKNOWN = 2;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::KNOWN   => 'Рез известен',
            self::UNKNOWN => 'Рез неизвестен',
        };
    }
}

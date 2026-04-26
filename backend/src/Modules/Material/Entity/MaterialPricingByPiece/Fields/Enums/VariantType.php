<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum VariantType: int implements EnumInterface
{
    case REGULAR = 1;
    case SINGLE_SIDED = 2;
    case DOUBLE_SIDED = 3;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::REGULAR         => 'Обычная',
            self::SINGLE_SIDED => 'Односторонняя (4+0)',
            self::DOUBLE_SIDED => 'Двухсторонняя (4+4)',
        };
    }
}

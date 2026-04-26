<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum AreaRangeType: int implements EnumInterface
{
    case UP_TO_50 = 1;
    case UP_TO_150 = 2;
    case UP_TO_300 = 3;
    case UP_TO_500 = 4;
    case OVER_500 = 5;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::UP_TO_50   => 'До 50 м2',
            self::UP_TO_150  => 'До 150 м2',
            self::UP_TO_300  => 'До 300 м2',
            self::UP_TO_500  => 'До 500 м2',
            self::OVER_500   => 'Более 500 м2',
        };
    }
}

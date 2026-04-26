<?php

declare(strict_types=1);

namespace App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums;

use App\Components\Enum\EnumInterface;
use Override;

enum DpiType: int implements EnumInterface
{
    case DPI_360 = 1;
    case DPI_600 = 2;
    case DPI_720 = 3;
    case DPI_1440 = 4;
    case DPI_1441 = 5;

    #[Override]
    public function getLabel(): string
    {
        return match ($this) {
            self::DPI_360  => '360 dpi',
            self::DPI_600  => '600 dpi',
            self::DPI_720  => '720 dpi',
            self::DPI_1440 => '1440 dpi',
            self::DPI_1441 => '1441 dpi',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Processing\Entity\Processing\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum ProcessingType: int implements EnumInterface
{
    case SQUARE_METER_ALL_AREA = 1;
    case LINEAR_METER_PERIMETER = 2;
    case LINEAR_METER_TWO_SIDES_MAX_LENGTH = 3;
    case PERCENT_OF_PRINT_COST = 4;
    case PERCENT_OF_TOTAL_COST = 5;
    case CUTTING_LENGTH_PER_METER = 6;
    case PRICE_PER_PIECE = 7;
    case COMPLEXITY_COEFFICIENT = 8;

    public function getLabel(): string
    {
        return match ($this) {
            self::SQUARE_METER_ALL_AREA           => 'метр квадратный по всей площади',
            self::LINEAR_METER_PERIMETER          => 'метр погонный по всему периметру',
            self::LINEAR_METER_TWO_SIDES_MAX_LENGTH => 'метр погонный по двум сторонам мак. по длине',
            self::PERCENT_OF_PRINT_COST           => 'процент от стоимости печати',
            self::PERCENT_OF_TOTAL_COST           => 'процент от итоговой стоимости',
            self::CUTTING_LENGTH_PER_METER        => 'по длине реза. за метр',
            self::PRICE_PER_PIECE                 => 'Цена за штуку',
            self::COMPLEXITY_COEFFICIENT          => 'Коэффициент сложности',
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderSection\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum SectionType: int implements EnumInterface
{
    case SECTION_1 = 1;
    case SECTION_2 = 2;
    case SECTION_3 = 3;
    case SECTION_4 = 4;
    case SECTION_5 = 5;
    case SECTION_6 = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::SECTION_1 => 'Секция 1',
            self::SECTION_2 => 'Секция 2',
            self::SECTION_3 => 'Секция 3',
            self::SECTION_4 => 'Секция 4',
            self::SECTION_5 => 'Секция 5',
            self::SECTION_6 => 'Секция 6',
        };
    }
}

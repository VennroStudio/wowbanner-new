<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\Client\Fields;

use App\Components\Enum\EnumInterface;

enum Docs: int implements EnumInterface
{
    case EDO              = 1;
    case POWER_OF_ATTORNEY = 2;
    case WITHOUT_DOCS     = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::EDO              => 'ЭДО',
            self::POWER_OF_ATTORNEY => 'Доверенность или печать',
            self::WITHOUT_DOCS     => 'Б/Д',
        };
    }
}

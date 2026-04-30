<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\Order\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum StorageType: int implements EnumInterface
{
    case ND = 1;
    case KP = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::ND => 'НД',
            self::KP => 'КП',
        };
    }
}

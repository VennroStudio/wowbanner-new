<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\ClientPhone\Fields;

use App\Components\Enum\EnumInterface;

enum PhoneType: int implements EnumInterface
{
    case MAIN       = 1;
    case ADDITIONAL = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MAIN       => 'Основной',
            self::ADDITIONAL => 'Дополнительный',
        };
    }
}

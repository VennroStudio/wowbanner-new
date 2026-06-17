<?php

declare(strict_types=1);

namespace App\Modules\Client\Entity\Client\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum ClientType: int implements EnumInterface
{
    case INDIVIDUAL = 1;
    case LEGAL = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'Физическое лицо',
            self::LEGAL      => 'Юридическое лицо',
        };
    }
}

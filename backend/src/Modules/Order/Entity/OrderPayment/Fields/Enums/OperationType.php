<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderPayment\Fields\Enums;

use App\Components\Enum\EnumInterface;

enum OperationType: int implements EnumInterface
{
    case EXPENSE = 1;
    case INCOME = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::EXPENSE => 'Расход',
            self::INCOME => 'Приход',
        };
    }
}

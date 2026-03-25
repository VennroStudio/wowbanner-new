<?php

declare(strict_types=1);

namespace App\Components\ReadModel;

trait FromRowsTrait
{
    public static function fromRows(array $rows): array
    {
        return array_map(static::fromRow(...), $rows);
    }
}

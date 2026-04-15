<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\Production\GetById;

final readonly class ProductionGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}

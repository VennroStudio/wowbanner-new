<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\Product\GetById;

final readonly class ProductGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}

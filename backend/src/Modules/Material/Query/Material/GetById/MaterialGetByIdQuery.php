<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\Material\GetById;

final readonly class MaterialGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}

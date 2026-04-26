<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetById;

final readonly class MaterialOptionGetByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetBySelect;

final readonly class MaterialOptionGetBySelectQuery
{
    public function __construct(
        public int $materialId,
    ) {}
}

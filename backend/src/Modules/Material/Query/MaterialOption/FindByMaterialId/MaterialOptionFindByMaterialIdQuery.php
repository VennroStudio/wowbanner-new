<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\FindByMaterialId;

final readonly class MaterialOptionFindByMaterialIdQuery
{
    public function __construct(
        public int $materialId,
    ) {}
}

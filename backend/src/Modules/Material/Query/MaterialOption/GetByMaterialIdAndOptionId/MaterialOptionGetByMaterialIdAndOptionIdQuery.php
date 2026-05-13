<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialOption\GetByMaterialIdAndOptionId;

final readonly class MaterialOptionGetByMaterialIdAndOptionIdQuery
{
    public function __construct(
        public int $materialId,
        public int $optionId,
    ) {}
}

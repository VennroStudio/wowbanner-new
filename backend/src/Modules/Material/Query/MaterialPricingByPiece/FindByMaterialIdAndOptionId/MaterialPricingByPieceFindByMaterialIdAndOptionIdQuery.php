<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingByPiece\FindByMaterialIdAndOptionId;

final readonly class MaterialPricingByPieceFindByMaterialIdAndOptionIdQuery
{
    public function __construct(
        public int $materialId,
        public int $optionId,
    ) {}
}

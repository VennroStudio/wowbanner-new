<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingCut\FindByMaterialIdAndOptionId;

final readonly class MaterialPricingCutFindByMaterialIdAndOptionIdQuery
{
    public function __construct(
        public int $materialId,
        public int $optionId,
    ) {}
}

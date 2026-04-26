<?php

declare(strict_types=1);

namespace App\Modules\Material\Query\MaterialPricingByArea\FindByMaterialIdAndOptionId;

final readonly class MaterialPricingByAreaFindByMaterialIdAndOptionIdQuery
{
    public function __construct(
        public int $materialId,
        public int $optionId,
    ) {}
}

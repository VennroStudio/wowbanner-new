<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\ProductionMaterial\FindByProductionIds;

final readonly class ProductionMaterialFindByProductionIdsQuery
{
    /**
     * @param list<int> $productionIds
     */
    public function __construct(
        public array $productionIds,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Production\Query\ProductionPrint\FindByProductionIds;

final readonly class ProductionPrintFindByProductionIdsQuery
{
    /**
     * @param list<int> $productionIds
     */
    public function __construct(
        public array $productionIds,
    ) {}
}

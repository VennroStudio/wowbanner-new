<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductMaterial\FindByProductIds;

final readonly class ProductMaterialFindByProductIdsQuery
{
    /**
     * @param list<int> $ProductIds
     */
    public function __construct(
        public array $ProductIds,
    ) {}
}

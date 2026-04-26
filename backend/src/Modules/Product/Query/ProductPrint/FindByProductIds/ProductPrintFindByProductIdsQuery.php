<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductPrint\FindByProductIds;

final readonly class ProductPrintFindByProductIdsQuery
{
    /**
     * @param list<int> $productIds
     */
    public function __construct(
        public array $productIds,
    ) {}
}

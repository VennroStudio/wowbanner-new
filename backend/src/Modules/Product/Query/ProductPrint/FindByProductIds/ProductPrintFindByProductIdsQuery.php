<?php

declare(strict_types=1);

namespace App\Modules\Product\Query\ProductPrint\FindByProductIds;

final readonly class ProductPrintFindByProductIdsQuery
{
    /**
     * @param list<int> $ProductIds
     */
    public function __construct(
        public array $ProductIds,
    ) {}
}

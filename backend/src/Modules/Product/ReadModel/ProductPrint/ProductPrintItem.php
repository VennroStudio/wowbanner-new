<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint;

final readonly class ProductPrintItem
{
    public function __construct(
        public ?int $id,
        public int $printId,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial;

final readonly class ProductMaterialItem
{
    public function __construct(
        public ?int $id,
        public int $materialOptionId,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Update;

final readonly class UpdateProductMaterialCommand
{
    public function __construct(
        public int $id,
        public int $ProductId,
        public int $materialOptionId,
    ) {}
}

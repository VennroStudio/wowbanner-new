<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Create;

final readonly class CreateProductMaterialCommand
{
    public function __construct(
        public int $ProductId,
        public int $materialOptionId,
    ) {}
}

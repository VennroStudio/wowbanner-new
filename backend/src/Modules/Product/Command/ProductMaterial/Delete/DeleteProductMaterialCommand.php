<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductMaterial\Delete;

final readonly class DeleteProductMaterialCommand
{
    public function __construct(
        public int $id,
    ) {}
}

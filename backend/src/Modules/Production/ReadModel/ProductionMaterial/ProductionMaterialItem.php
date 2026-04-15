<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionMaterial;

final readonly class ProductionMaterialItem
{
    public function __construct(
        public ?int $id,
        public int $materialOptionId,
    ) {}
}

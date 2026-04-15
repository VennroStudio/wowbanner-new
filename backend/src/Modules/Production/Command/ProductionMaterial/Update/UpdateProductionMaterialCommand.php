<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Update;

final readonly class UpdateProductionMaterialCommand
{
    public function __construct(
        public int $id,
        public int $productionId,
        public int $materialOptionId,
    ) {}
}

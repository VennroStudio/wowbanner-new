<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Create;

final readonly class CreateProductionMaterialCommand
{
    public function __construct(
        public int $productionId,
        public int $materialOptionId,
    ) {}
}

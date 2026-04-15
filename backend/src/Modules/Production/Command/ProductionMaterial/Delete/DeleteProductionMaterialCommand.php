<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionMaterial\Delete;

final readonly class DeleteProductionMaterialCommand
{
    public function __construct(
        public int $id,
    ) {}
}

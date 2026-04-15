<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Update;

final readonly class UpdateProductionPrintCommand
{
    public function __construct(
        public int $id,
        public int $productionId,
        public int $printId,
    ) {}
}

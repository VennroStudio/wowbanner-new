<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Create;

final readonly class CreateProductionPrintCommand
{
    public function __construct(
        public int $productionId,
        public int $printId,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Production\Command\ProductionPrint\Delete;

final readonly class DeleteProductionPrintCommand
{
    public function __construct(
        public int $id,
    ) {}
}

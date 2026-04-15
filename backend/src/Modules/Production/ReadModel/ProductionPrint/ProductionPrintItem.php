<?php

declare(strict_types=1);

namespace App\Modules\Production\ReadModel\ProductionPrint;

final readonly class ProductionPrintItem
{
    public function __construct(
        public ?int $id,
        public int $printId,
    ) {}
}

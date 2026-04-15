<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Delete;

final readonly class DeleteProductPrintCommand
{
    public function __construct(
        public int $id,
    ) {}
}

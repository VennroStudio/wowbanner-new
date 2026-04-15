<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Update;

final readonly class UpdateProductPrintCommand
{
    public function __construct(
        public int $id,
        public int $ProductId,
        public int $printId,
    ) {}
}

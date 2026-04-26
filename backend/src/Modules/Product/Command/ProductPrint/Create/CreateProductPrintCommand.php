<?php

declare(strict_types=1);

namespace App\Modules\Product\Command\ProductPrint\Create;

final readonly class CreateProductPrintCommand
{
    public function __construct(
        public int $productId,
        public int $printId,
    ) {}
}

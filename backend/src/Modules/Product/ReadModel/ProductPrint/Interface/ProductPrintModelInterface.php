<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductPrint\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface ProductPrintModelInterface extends ReadModelInterface
{
    public function getProductId(): int;
}

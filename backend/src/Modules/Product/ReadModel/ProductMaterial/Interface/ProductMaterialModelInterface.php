<?php

declare(strict_types=1);

namespace App\Modules\Product\ReadModel\ProductMaterial\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface ProductMaterialModelInterface extends ReadModelInterface
{
    public function getProductId(): int;
}

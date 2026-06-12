<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByArea\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialPricingByAreaModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;

    public function getOptionId(): int;
}

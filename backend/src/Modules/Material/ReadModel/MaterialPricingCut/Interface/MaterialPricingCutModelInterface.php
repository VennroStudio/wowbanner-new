<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingCut\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialPricingCutModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;

    public function getOptionId(): int;
}

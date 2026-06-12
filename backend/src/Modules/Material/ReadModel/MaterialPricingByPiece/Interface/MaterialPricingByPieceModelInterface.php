<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialPricingByPiece\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialPricingByPieceModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;

    public function getOptionId(): int;
}

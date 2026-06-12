<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialOption\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialOptionModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;
}

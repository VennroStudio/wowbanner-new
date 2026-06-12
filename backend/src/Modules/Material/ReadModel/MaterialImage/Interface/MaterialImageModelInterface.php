<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialImage\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialImageModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;
}

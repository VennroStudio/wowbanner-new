<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\ProcessingImage\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface ProcessingImageModelInterface extends ReadModelInterface
{
    public function getProcessingId(): int;
}

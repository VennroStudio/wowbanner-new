<?php

declare(strict_types=1);

namespace App\Modules\Material\ReadModel\MaterialProcessing\Interface;

use App\Components\ReadModel\ReadModelInterface;

interface MaterialProcessingModelInterface extends ReadModelInterface
{
    public function getMaterialId(): int;

    public function getOptionId(): int;

    public function getProcessingId(): int;
}

<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\ProcessingImage\Interface;

interface ProcessingImageModelInterface
{
    public function getId(): int;

    public function getProcessingId(): int;

    public function toArray(): array;
}

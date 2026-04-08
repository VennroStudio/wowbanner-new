<?php

declare(strict_types=1);

namespace App\Modules\Processing\ReadModel\Processing\Interface;

interface ProcessingModelInterface
{
    public function getId(): int;

    public function toArray(): array;
}

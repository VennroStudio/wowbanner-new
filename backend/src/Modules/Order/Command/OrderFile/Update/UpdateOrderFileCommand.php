<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Update;

final readonly class UpdateOrderFileCommand
{
    public function __construct(
        public int $id,
        public string $tmpFilePath,
        public string $originalName,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderFile\Create;

final readonly class CreateOrderFileCommand
{
    public function __construct(
        public int $orderId,
        public string $tmpFilePath,
        public string $originalName,
    ) {}
}

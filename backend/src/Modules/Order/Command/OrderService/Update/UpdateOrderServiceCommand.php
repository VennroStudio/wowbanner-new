<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderService\Update;

final readonly class UpdateOrderServiceCommand
{
    public function __construct(
        public int $id,
        public int $serviceType,
        public string $price,
        public ?string $note = null,
    ) {}
}

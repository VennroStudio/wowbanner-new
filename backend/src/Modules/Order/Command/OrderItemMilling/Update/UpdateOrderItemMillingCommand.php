<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemMilling\Update;

final readonly class UpdateOrderItemMillingCommand
{
    public function __construct(
        public int $id,
        public ?int $sourceItemId,
        public int $printId,
        public string $material,
        public string $price,
        public ?int $performerId = null,
        public ?string $note = null,
        public bool $printed = false,
        public bool $ready = false,
    ) {}
}

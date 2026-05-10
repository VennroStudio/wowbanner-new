<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItem\Create;

final readonly class CreateOrderItemCommand
{
    public function __construct(
        public int $orderId,
        public ?int $sourceItemId,
        public int $printId,
        public int $productId,
        public int $materialId,
        public int $optionId,
        public int $dpiType,
        public int $variantType,
        public string $width,
        public string $height,
        public int $quantity,
        public string $price,
        public ?int $performerId = null,
        public ?string $note = null,
        public bool $printed = false,
        public bool $ready = false,
    ) {}
}

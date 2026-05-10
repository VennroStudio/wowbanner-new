<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItemMilling;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderItemMillingItem
{
    public function __construct(
        public ?int $id,
        public ?int $sourceItemId,
        #[Assert\NotBlank(message: 'validation.order_item_milling_print_id_required')]
        #[Assert\GreaterThan(0)]
        public int $printId,
        #[Assert\NotBlank(message: 'validation.order_item_milling_material_required')]
        public string $material,
        #[Assert\NotBlank(message: 'validation.order_item_milling_price_required')]
        public string $price,
        public ?int $performerId = null,
        public ?string $note = null,
        public bool $printed = false,
        public bool $ready = false,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItem;

use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingItem;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderItemItem
{
    public function __construct(
        public ?int $id,
        public ?int $sourceItemId,
        #[Assert\NotBlank(message: 'validation.order_item_print_id_required')]
        #[Assert\GreaterThan(0)]
        public int $printId,
        #[Assert\NotBlank(message: 'validation.order_item_product_id_required')]
        #[Assert\GreaterThan(0)]
        public int $productId,
        #[Assert\NotBlank(message: 'validation.order_item_material_id_required')]
        #[Assert\GreaterThan(0)]
        public int $materialId,
        #[Assert\NotBlank(message: 'validation.order_item_option_id_required')]
        #[Assert\GreaterThan(0)]
        public int $optionId,
        #[Assert\NotBlank(message: 'validation.order_item_dpi_type_required')]
        public int $dpiType,
        #[Assert\NotBlank(message: 'validation.order_item_variant_type_required')]
        public int $variantType,
        #[Assert\NotBlank(message: 'validation.order_item_width_required')]
        public string $width,
        #[Assert\NotBlank(message: 'validation.order_item_height_required')]
        public string $height,
        #[Assert\NotBlank(message: 'validation.order_item_quantity_required')]
        #[Assert\GreaterThan(0)]
        public int $quantity,
        #[Assert\NotBlank(message: 'validation.order_item_price_required')]
        public string $price,
        public ?int $performerId = null,
        public ?string $note = null,
        public bool $printed = false,
        public bool $ready = false,
        /** @var list<OrderItemProcessingItem> */
        #[Assert\Valid]
        public array $processings = [],
    ) {}
}

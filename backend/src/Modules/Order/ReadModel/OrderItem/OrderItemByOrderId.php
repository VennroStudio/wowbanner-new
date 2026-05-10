<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItem;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Material\Entity\MaterialPricingByArea\Fields\Enums\DpiType;
use App\Modules\Material\Entity\MaterialPricingByPiece\Fields\Enums\VariantType;
use App\Modules\Order\ReadModel\OrderItem\Interface\OrderItemModelInterface;
use Override;

final readonly class OrderItemByOrderId implements OrderItemModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public ?int $sourceItemId,
        public int $printId,
        public int $productId,
        public int $materialId,
        public int $optionId,
        public DpiType $dpiType,
        public VariantType $variantType,
        public string $width,
        public string $height,
        public int $quantity,
        public ?int $performerId,
        public ?string $note,
        public bool $printed,
        public bool $ready,
        public string $price,
    ) {}

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     source_item_id: int|null,
     *     print_id: int,
     *     product_id: int,
     *     material_id: int,
     *     option_id: int,
     *     dpi_type: int,
     *     variant_type: int,
     *     width: string,
     *     height: string,
     *     quantity: int,
     *     performer_id: int|null,
     *     note: string|null,
     *     printed: bool|int,
     *     ready: bool|int,
     *     price: string
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            orderId: (int) $row['order_id'],
            sourceItemId: $row['source_item_id'] !== null ? (int) $row['source_item_id'] : null,
            printId: (int) $row['print_id'],
            productId: (int) $row['product_id'],
            materialId: (int) $row['material_id'],
            optionId: (int) $row['option_id'],
            dpiType: DpiType::from((int) $row['dpi_type']),
            variantType: VariantType::from((int) $row['variant_type']),
            width: $row['width'],
            height: $row['height'],
            quantity: (int) $row['quantity'],
            performerId: $row['performer_id'] !== null ? (int) $row['performer_id'] : null,
            note: $row['note'],
            printed: (bool) $row['printed'],
            ready: (bool) $row['ready'],
            price: $row['price'],
        );
    }

    #[Override]
    public function getId(): int
    {
        return $this->id;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'source_item_id' => $this->sourceItemId,
            'print_id' => $this->printId,
            'product_id' => $this->productId,
            'material_id' => $this->materialId,
            'option_id' => $this->optionId,
            'dpi_type' => ['id' => $this->dpiType->value, 'label' => $this->dpiType->getLabel()],
            'variant_type' => ['id' => $this->variantType->value, 'label' => $this->variantType->getLabel()],
            'width' => $this->width,
            'height' => $this->height,
            'quantity' => $this->quantity,
            'performer_id' => $this->performerId,
            'note' => $this->note,
            'printed' => $this->printed,
            'ready' => $this->ready,
            'price' => $this->price,
        ];
    }
}

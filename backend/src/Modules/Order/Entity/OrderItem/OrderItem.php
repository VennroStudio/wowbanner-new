<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItem;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
#[ORM\Index(name: 'idx_order_item_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_item_print_type_id', columns: ['print_type_id'])]
#[ORM\Index(name: 'idx_order_item_product_id', columns: ['product_id'])]
#[ORM\Index(name: 'idx_order_item_material_id', columns: ['material_id'])]
#[ORM\Index(name: 'idx_order_item_option_id', columns: ['option_id'])]
#[ORM\Index(name: 'idx_order_item_performer_id', columns: ['performer_id'])]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $printTypeId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $productId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $materialId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $optionId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $dpiType;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $variantType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $width;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $height;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $quantity;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private(set) ?int $performerId;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $note;

    #[ORM\Column(type: Types::BOOLEAN)]
    private(set) bool $printed;

    #[ORM\Column(type: Types::BOOLEAN)]
    private(set) bool $ready;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    private function __construct(
        int $orderId,
        int $printTypeId,
        int $productId,
        int $materialId,
        int $optionId,
        int $dpiType,
        int $variantType,
        string $width,
        string $height,
        int $quantity,
        ?int $performerId,
        ?string $note,
        bool $printed,
        bool $ready,
        string $price,
    ) {
        $this->orderId = $orderId;
        $this->printTypeId = $printTypeId;
        $this->productId = $productId;
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->dpiType = $dpiType;
        $this->variantType = $variantType;
        $this->width = $width;
        $this->height = $height;
        $this->quantity = $quantity;
        $this->performerId = $performerId;
        $this->note = $note;
        $this->printed = $printed;
        $this->ready = $ready;
        $this->price = $price;
    }

    public static function create(
        int $orderId,
        int $printTypeId,
        int $productId,
        int $materialId,
        int $optionId,
        int $dpiType,
        int $variantType,
        string $width,
        string $height,
        int $quantity,
        ?int $performerId,
        ?string $note,
        string $price,
        bool $printed = false,
        bool $ready = false,
    ): self {
        return new self(
            orderId: $orderId,
            printTypeId: $printTypeId,
            productId: $productId,
            materialId: $materialId,
            optionId: $optionId,
            dpiType: $dpiType,
            variantType: $variantType,
            width: $width,
            height: $height,
            quantity: $quantity,
            performerId: $performerId,
            note: $note,
            printed: $printed,
            ready: $ready,
            price: $price,
        );
    }

    public function edit(
        int $printTypeId,
        int $productId,
        int $materialId,
        int $optionId,
        int $dpiType,
        int $variantType,
        string $width,
        string $height,
        int $quantity,
        ?int $performerId,
        ?string $note,
        bool $printed,
        bool $ready,
        string $price,
    ): void {
        $this->printTypeId = $printTypeId;
        $this->productId = $productId;
        $this->materialId = $materialId;
        $this->optionId = $optionId;
        $this->dpiType = $dpiType;
        $this->variantType = $variantType;
        $this->width = $width;
        $this->height = $height;
        $this->quantity = $quantity;
        $this->performerId = $performerId;
        $this->note = $note;
        $this->printed = $printed;
        $this->ready = $ready;
        $this->price = $price;
    }
}

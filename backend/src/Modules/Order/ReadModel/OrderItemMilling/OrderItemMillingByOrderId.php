<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItemMilling;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\ReadModel\OrderItemMilling\Interface\OrderItemMillingModelInterface;
use Override;

final readonly class OrderItemMillingByOrderId implements OrderItemMillingModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public ?int $sourceItemId,
        public int $printId,
        public string $material,
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
     *     material: string,
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
            material: $row['material'],
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
            'material' => $this->material,
            'performer_id' => $this->performerId,
            'note' => $this->note,
            'printed' => $this->printed,
            'ready' => $this->ready,
            'price' => $this->price,
        ];
    }
}

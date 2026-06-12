<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItemProcessing;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\ReadModel\OrderItemProcessing\Interface\OrderItemProcessingModelInterface;
use Override;

final readonly class OrderItemProcessingDetails implements OrderItemProcessingModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderItemId,
        public int $processingId,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'            => 'id',
            'order_item_id' => 'order_item_id',
            'processing_id' => 'processing_id',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_item_id: int,
     *     processing_id: int
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderItemId: (int)$row['order_item_id'],
            processingId: (int)$row['processing_id'],
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
            'id'            => $this->id,
            'processing_id' => $this->processingId,
        ];
    }
}

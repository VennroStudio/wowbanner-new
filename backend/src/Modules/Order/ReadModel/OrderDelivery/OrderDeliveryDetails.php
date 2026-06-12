<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderDelivery;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\OrderDelivery\Fields\Enums\DeliveryType;
use App\Modules\Order\ReadModel\OrderDelivery\Interface\OrderDeliveryModelInterface;
use Override;

final readonly class OrderDeliveryDetails implements OrderDeliveryModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public DeliveryType $deliveryType,
        public ?string $address,
        public ?string $comment,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'            => 'id',
            'order_id'      => 'order_id',
            'delivery_type' => 'delivery_type',
            'address'       => 'address',
            'comment'       => 'comment',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     delivery_type: int,
     *     address: string|null,
     *     comment: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            deliveryType: DeliveryType::from((int)$row['delivery_type']),
            address: $row['address'],
            comment: $row['comment'],
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
            'delivery_type' => ['id' => $this->deliveryType->value, 'label' => $this->deliveryType->getLabel()],
            'address'       => $this->address,
            'comment'       => $this->comment,
        ];
    }
}

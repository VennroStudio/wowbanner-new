<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderService;

use App\Components\ReadModel\FromRowsTrait;
use App\Modules\Order\Entity\OrderService\Fields\Enums\ServiceType;
use App\Modules\Order\ReadModel\OrderService\Interface\OrderServiceModelInterface;
use Override;

final readonly class OrderServiceDetails implements OrderServiceModelInterface
{
    use FromRowsTrait;

    public function __construct(
        public int $id,
        public int $orderId,
        public ServiceType $serviceType,
        public string $price,
        public ?string $note,
    ) {}

    /**
     * @return array<string, string>
     */
    public static function fields(): array
    {
        return [
            'id'           => 'id',
            'order_id'     => 'order_id',
            'service_type' => 'service_type',
            'price'        => 'price',
            'note'         => 'note',
        ];
    }

    /**
     * @param array{
     *     id: int,
     *     order_id: int,
     *     service_type: int,
     *     price: string,
     *     note: string|null
     * } $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            orderId: (int)$row['order_id'],
            serviceType: ServiceType::from((int)$row['service_type']),
            price: $row['price'],
            note: $row['note'],
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
            'id'           => $this->id,
            'service_type' => ['id' => $this->serviceType->value, 'label' => $this->serviceType->getLabel()],
            'price'        => $this->price,
            'note'         => $this->note,
        ];
    }
}

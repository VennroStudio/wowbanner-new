<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderDelivery;

use App\Modules\Order\Entity\OrderDelivery\Fields\Enums\DeliveryType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_deliveries')]
#[ORM\Index(name: 'idx_order_delivery_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_delivery_type', columns: ['delivery_type'])]
class OrderDelivery
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER, enumType: DeliveryType::class)]
    private(set) DeliveryType $deliveryType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $address;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $comment;

    private function __construct(
        int $orderId,
        DeliveryType $deliveryType,
        ?string $address,
        ?string $comment,
    ) {
        $this->orderId = $orderId;
        $this->deliveryType = $deliveryType;
        $this->address = $address;
        $this->comment = $comment;
    }

    public static function create(
        int $orderId,
        DeliveryType $deliveryType,
        ?string $address,
        ?string $comment,
    ): self {
        return new self($orderId, $deliveryType, $address, $comment);
    }

    public function edit(
        DeliveryType $deliveryType,
        ?string $address,
        ?string $comment,
    ): void {
        $this->deliveryType = $deliveryType;
        $this->address = $address;
        $this->comment = $comment;
    }
}

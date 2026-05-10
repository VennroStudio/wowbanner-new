<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderService;

use App\Modules\Order\Entity\OrderService\Fields\Enums\ServiceType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_services')]
#[ORM\Index(name: 'idx_order_service_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_service_type', columns: ['service_type'])]
class OrderService
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER, enumType: ServiceType::class)]
    private(set) ServiceType $serviceType;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private(set) string $price;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private(set) ?string $note;

    private function __construct(
        int $orderId,
        ServiceType $serviceType,
        string $price,
        ?string $note,
    ) {
        $this->orderId = $orderId;
        $this->serviceType = $serviceType;
        $this->price = $price;
        $this->note = $note;
    }

    public static function create(
        int $orderId,
        ServiceType $serviceType,
        string $price,
        ?string $note,
    ): self {
        return new self(
            orderId: $orderId,
            serviceType: $serviceType,
            price: $price,
            note: $note,
        );
    }

    public function edit(
        ServiceType $serviceType,
        string $price,
        ?string $note,
    ): void {
        $this->serviceType = $serviceType;
        $this->price = $price;
        $this->note = $note;
    }
}

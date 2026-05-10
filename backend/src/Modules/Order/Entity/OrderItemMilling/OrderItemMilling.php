<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemMilling;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_item_millings')]
#[ORM\Index(name: 'idx_order_item_milling_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_order_item_milling_print_id', columns: ['print_id'])]
#[ORM\Index(name: 'idx_order_item_milling_performer_id', columns: ['performer_id'])]
class OrderItemMilling
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $printId;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private(set) string $material;

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
        int $printId,
        string $material,
        ?int $performerId,
        ?string $note,
        bool $printed,
        bool $ready,
        string $price,
    ) {
        $this->orderId = $orderId;
        $this->printId = $printId;
        $this->material = $material;
        $this->performerId = $performerId;
        $this->note = $note;
        $this->printed = $printed;
        $this->ready = $ready;
        $this->price = $price;
    }

    public static function create(
        int $orderId,
        int $printId,
        string $material,
        ?int $performerId,
        ?string $note,
        string $price,
        bool $printed = false,
        bool $ready = false,
    ): self {
        return new self(
            orderId: $orderId,
            printId: $printId,
            material: $material,
            performerId: $performerId,
            note: $note,
            printed: $printed,
            ready: $ready,
            price: $price,
        );
    }

    public function edit(
        int $printId,
        string $material,
        ?int $performerId,
        ?string $note,
        bool $printed,
        bool $ready,
        string $price,
    ): void {
        $this->printId = $printId;
        $this->material = $material;
        $this->performerId = $performerId;
        $this->note = $note;
        $this->printed = $printed;
        $this->ready = $ready;
        $this->price = $price;
    }
}

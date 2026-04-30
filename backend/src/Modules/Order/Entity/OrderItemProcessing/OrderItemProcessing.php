<?php

declare(strict_types=1);

namespace App\Modules\Order\Entity\OrderItemProcessing;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_item_processings')]
#[ORM\Index(name: 'idx_order_item_processing_item_id', columns: ['order_item_id'])]
#[ORM\Index(name: 'idx_order_item_processing_processing_id', columns: ['processing_id'])]
class OrderItemProcessing
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private(set) ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $orderItemId;

    #[ORM\Column(type: Types::INTEGER)]
    private(set) int $processingId;

    private function __construct(int $orderItemId, int $processingId)
    {
        $this->orderItemId = $orderItemId;
        $this->processingId = $processingId;
    }

    public static function create(int $orderItemId, int $processingId): self
    {
        return new self($orderItemId, $processingId);
    }

    public function edit(int $processingId): void
    {
        $this->processingId = $processingId;
    }
}

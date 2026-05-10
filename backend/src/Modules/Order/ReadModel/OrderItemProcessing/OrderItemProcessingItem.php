<?php

declare(strict_types=1);

namespace App\Modules\Order\ReadModel\OrderItemProcessing;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderItemProcessingItem
{
    public function __construct(
        public ?int $id,
        #[Assert\NotBlank(message: 'validation.order_item_processing_processing_id_required')]
        #[Assert\GreaterThan(0)]
        public int $processingId,
    ) {}
}

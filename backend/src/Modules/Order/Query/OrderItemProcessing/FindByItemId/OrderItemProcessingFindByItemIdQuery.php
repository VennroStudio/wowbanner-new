<?php

declare(strict_types=1);

namespace App\Modules\Order\Query\OrderItemProcessing\FindByItemId;

final readonly class OrderItemProcessingFindByItemIdQuery
{
    public function __construct(
        public int $itemId,
    ) {}
}

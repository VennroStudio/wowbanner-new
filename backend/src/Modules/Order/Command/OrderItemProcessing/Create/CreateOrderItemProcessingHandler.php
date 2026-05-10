<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Create;

use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessing;
use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessingRepository;

final readonly class CreateOrderItemProcessingHandler
{
    public function __construct(
        private OrderItemProcessingRepository $repository,
    ) {}

    public function handle(CreateOrderItemProcessingCommand $command): void
    {
        $orderItemProcessing = OrderItemProcessing::create(
            orderItemId: $command->orderItemId,
            processingId: $command->processingId,
        );

        $this->repository->add($orderItemProcessing);
    }
}

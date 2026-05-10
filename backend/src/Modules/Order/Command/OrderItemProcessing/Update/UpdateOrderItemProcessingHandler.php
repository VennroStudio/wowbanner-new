<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Update;

use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessingRepository;

final readonly class UpdateOrderItemProcessingHandler
{
    public function __construct(
        private OrderItemProcessingRepository $repository,
    ) {}

    public function handle(UpdateOrderItemProcessingCommand $command): void
    {
        $orderItemProcessing = $this->repository->getById($command->id);

        $orderItemProcessing->edit(
            processingId: $command->processingId,
        );
    }
}

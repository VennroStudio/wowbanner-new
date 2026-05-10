<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemProcessing\Delete;

use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessingRepository;

final readonly class DeleteOrderItemProcessingHandler
{
    public function __construct(
        private OrderItemProcessingRepository $repository,
    ) {}

    public function handle(DeleteOrderItemProcessingCommand $command): void
    {
        $orderItemProcessing = $this->repository->getById($command->id);

        $this->repository->remove($orderItemProcessing);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItem\Delete;

use App\Modules\Order\Entity\OrderItem\OrderItemRepository;

final readonly class DeleteOrderItemHandler
{
    public function __construct(
        private OrderItemRepository $repository,
    ) {}

    public function handle(DeleteOrderItemCommand $command): void
    {
        $orderItem = $this->repository->getById($command->id);

        $this->repository->remove($orderItem);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemMilling\Delete;

use App\Modules\Order\Entity\OrderItemMilling\OrderItemMillingRepository;

final readonly class DeleteOrderItemMillingHandler
{
    public function __construct(
        private OrderItemMillingRepository $repository,
    ) {}

    public function handle(DeleteOrderItemMillingCommand $command): void
    {
        $orderItemMilling = $this->repository->getById($command->id);

        $this->repository->remove($orderItemMilling);
    }
}

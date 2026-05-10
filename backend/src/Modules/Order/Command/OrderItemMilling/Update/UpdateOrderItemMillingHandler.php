<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemMilling\Update;

use App\Modules\Order\Entity\OrderItemMilling\OrderItemMillingRepository;

final readonly class UpdateOrderItemMillingHandler
{
    public function __construct(
        private OrderItemMillingRepository $repository,
    ) {}

    public function handle(UpdateOrderItemMillingCommand $command): void
    {
        $orderItemMilling = $this->repository->getById($command->id);

        $orderItemMilling->edit(
            sourceItemId: $command->sourceItemId,
            printId: $command->printId,
            material: $command->material,
            performerId: $command->performerId,
            note: $command->note,
            printed: $command->printed,
            ready: $command->ready,
            price: $command->price,
        );
    }
}

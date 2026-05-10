<?php

declare(strict_types=1);

namespace App\Modules\Order\Command\OrderItemMilling\Create;

use App\Modules\Order\Entity\OrderItemMilling\OrderItemMilling;
use App\Modules\Order\Entity\OrderItemMilling\OrderItemMillingRepository;

final readonly class CreateOrderItemMillingHandler
{
    public function __construct(
        private OrderItemMillingRepository $repository,
    ) {}

    public function handle(CreateOrderItemMillingCommand $command): void
    {
        $orderItemMilling = OrderItemMilling::create(
            orderId: $command->orderId,
            sourceItemId: $command->sourceItemId,
            printId: $command->printId,
            material: $command->material,
            performerId: $command->performerId,
            note: $command->note,
            price: $command->price,
            printed: $command->printed,
            ready: $command->ready,
        );

        $this->repository->add($orderItemMilling);
    }
}

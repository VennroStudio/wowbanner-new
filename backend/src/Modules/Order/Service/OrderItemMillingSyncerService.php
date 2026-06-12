<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderItemMilling\Create\CreateOrderItemMillingCommand;
use App\Modules\Order\Command\OrderItemMilling\Create\CreateOrderItemMillingHandler;
use App\Modules\Order\Command\OrderItemMilling\Delete\DeleteOrderItemMillingCommand;
use App\Modules\Order\Command\OrderItemMilling\Delete\DeleteOrderItemMillingHandler;
use App\Modules\Order\Command\OrderItemMilling\Update\UpdateOrderItemMillingCommand;
use App\Modules\Order\Command\OrderItemMilling\Update\UpdateOrderItemMillingHandler;
use App\Modules\Order\Entity\OrderItemMilling\OrderItemMillingRepository;
use App\Modules\Order\ReadModel\OrderItemMilling\OrderItemMillingItem;

final readonly class OrderItemMillingSyncerService
{
    public function __construct(
        private OrderItemMillingRepository $repository,
        private CreateOrderItemMillingHandler $createHandler,
        private UpdateOrderItemMillingHandler $updateHandler,
        private DeleteOrderItemMillingHandler $deleteHandler,
    ) {}

    /**
     * @param list<OrderItemMillingItem> $items
     */
    public function sync(int $orderId, array $items): void
    {
        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn ($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn (OrderItemMillingItem $item) => $item->id, $items));

        foreach ($currentItems as $currentItem) {
            if (!\in_array($currentItem->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteOrderItemMillingCommand($currentItem->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderItemMillingCommand(
                    id: $item->id,
                    sourceItemId: $item->sourceItemId,
                    printId: $item->printId,
                    material: $item->material,
                    price: $item->price,
                    performerId: $item->performerId,
                    note: $item->note,
                    printed: $item->printed,
                    ready: $item->ready,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderItemMillingCommand(
                orderId: $orderId,
                sourceItemId: $item->sourceItemId,
                printId: $item->printId,
                material: $item->material,
                price: $item->price,
                performerId: $item->performerId,
                note: $item->note,
                printed: $item->printed,
                ready: $item->ready,
            ));
        }
    }
}

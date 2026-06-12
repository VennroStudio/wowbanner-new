<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderService\Create\CreateOrderServiceCommand;
use App\Modules\Order\Command\OrderService\Create\CreateOrderServiceHandler;
use App\Modules\Order\Command\OrderService\Delete\DeleteOrderServiceCommand;
use App\Modules\Order\Command\OrderService\Delete\DeleteOrderServiceHandler;
use App\Modules\Order\Command\OrderService\Update\UpdateOrderServiceCommand;
use App\Modules\Order\Command\OrderService\Update\UpdateOrderServiceHandler;
use App\Modules\Order\Entity\OrderService\OrderServiceRepository;
use App\Modules\Order\ReadModel\OrderService\OrderServiceItem;

final readonly class OrderServiceSyncerService
{
    public function __construct(
        private OrderServiceRepository $repository,
        private CreateOrderServiceHandler $createHandler,
        private UpdateOrderServiceHandler $updateHandler,
        private DeleteOrderServiceHandler $deleteHandler,
    ) {}

    /**
     * @param list<OrderServiceItem> $items
     */
    public function sync(int $orderId, array $items): void
    {
        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn ($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn (OrderServiceItem $item) => $item->id, $items));

        foreach ($currentItems as $currentItem) {
            if (!\in_array($currentItem->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteOrderServiceCommand($currentItem->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderServiceCommand(
                    id: $item->id,
                    serviceType: $item->serviceType,
                    price: $item->price,
                    note: $item->note,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderServiceCommand(
                orderId: $orderId,
                serviceType: $item->serviceType,
                price: $item->price,
                note: $item->note,
            ));
        }
    }
}

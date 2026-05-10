<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderItemProcessing\Create\CreateOrderItemProcessingCommand;
use App\Modules\Order\Command\OrderItemProcessing\Create\CreateOrderItemProcessingHandler;
use App\Modules\Order\Command\OrderItemProcessing\Delete\DeleteOrderItemProcessingCommand;
use App\Modules\Order\Command\OrderItemProcessing\Delete\DeleteOrderItemProcessingHandler;
use App\Modules\Order\Command\OrderItemProcessing\Update\UpdateOrderItemProcessingCommand;
use App\Modules\Order\Command\OrderItemProcessing\Update\UpdateOrderItemProcessingHandler;
use App\Modules\Order\Entity\OrderItemProcessing\OrderItemProcessingRepository;
use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingItem;

final readonly class OrderItemProcessingSyncerService
{
    public function __construct(
        private OrderItemProcessingRepository $repository,
        private CreateOrderItemProcessingHandler $createHandler,
        private UpdateOrderItemProcessingHandler $updateHandler,
        private DeleteOrderItemProcessingHandler $deleteHandler,
    ) {}

    /**
     * @param list<OrderItemProcessingItem> $items
     */
    public function sync(int $orderItemId, array $items): void
    {
        $currentItems = $this->repository->findByOrderItemId($orderItemId);
        $currentIds = array_map(static fn($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn(OrderItemProcessingItem $item) => $item->id, $items));

        foreach ($currentItems as $currentItem) {
            if (!in_array($currentItem->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteOrderItemProcessingCommand($currentItem->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderItemProcessingCommand(
                    id: $item->id,
                    processingId: $item->processingId,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderItemProcessingCommand(
                orderItemId: $orderItemId,
                processingId: $item->processingId,
            ));
        }
    }
}

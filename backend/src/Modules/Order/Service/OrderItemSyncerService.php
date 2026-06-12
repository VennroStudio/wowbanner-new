<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderItem\Create\CreateOrderItemCommand;
use App\Modules\Order\Command\OrderItem\Create\CreateOrderItemHandler;
use App\Modules\Order\Command\OrderItem\Delete\DeleteOrderItemCommand;
use App\Modules\Order\Command\OrderItem\Delete\DeleteOrderItemHandler;
use App\Modules\Order\Command\OrderItem\Update\UpdateOrderItemCommand;
use App\Modules\Order\Command\OrderItem\Update\UpdateOrderItemHandler;
use App\Modules\Order\Entity\OrderItem\OrderItem;
use App\Modules\Order\Entity\OrderItem\OrderItemRepository;
use App\Modules\Order\ReadModel\OrderItem\OrderItemItem;
use App\Modules\Order\ReadModel\OrderItemProcessing\OrderItemProcessingItem;

final readonly class OrderItemSyncerService
{
    public function __construct(
        private OrderItemRepository $repository,
        private CreateOrderItemHandler $createHandler,
        private UpdateOrderItemHandler $updateHandler,
        private DeleteOrderItemHandler $deleteHandler,
        private OrderItemProcessingSyncerService $processingSyncerService,
    ) {}

    /**
     * @param list<OrderItemItem> $items
     */
    public function sync(int $orderId, array $items): array
    {
        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn ($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn (OrderItemItem $item) => $item->id, $items));
        $pendingProcessings = [];

        foreach ($currentItems as $currentItem) {
            if (!\in_array($currentItem->id, $commandIds, true)) {
                $this->processingSyncerService->sync($currentItem->id, []);
                $this->deleteHandler->handle(new DeleteOrderItemCommand($currentItem->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && \in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderItemCommand(
                    id: $item->id,
                    sourceItemId: $item->sourceItemId,
                    printId: $item->printId,
                    productId: $item->productId,
                    materialId: $item->materialId,
                    optionId: $item->optionId,
                    dpiType: $item->dpiType,
                    variantType: $item->variantType,
                    width: $item->width,
                    height: $item->height,
                    quantity: $item->quantity,
                    price: $item->price,
                    performerId: $item->performerId,
                    note: $item->note,
                    printed: $item->printed,
                    ready: $item->ready,
                ));

                $pendingProcessings[] = [
                    'orderItem'   => $this->repository->getById($item->id),
                    'processings' => $item->processings,
                ];
            } else {
                $createdItem = $this->createHandler->handle(new CreateOrderItemCommand(
                    orderId: $orderId,
                    sourceItemId: $item->sourceItemId,
                    printId: $item->printId,
                    productId: $item->productId,
                    materialId: $item->materialId,
                    optionId: $item->optionId,
                    dpiType: $item->dpiType,
                    variantType: $item->variantType,
                    width: $item->width,
                    height: $item->height,
                    quantity: $item->quantity,
                    price: $item->price,
                    performerId: $item->performerId,
                    note: $item->note,
                    printed: $item->printed,
                    ready: $item->ready,
                ));

                $pendingProcessings[] = [
                    'orderItem'   => $createdItem,
                    'processings' => $item->processings,
                ];
            }
        }

        return $pendingProcessings;
    }

    /**
     * @param list<array{orderItem: OrderItem, processings: list<OrderItemProcessingItem>}> $pendingProcessings
     */
    public function syncProcessings(array $pendingProcessings): void
    {
        foreach ($pendingProcessings as $item) {
            $this->processingSyncerService->sync(
                orderItemId: (int)$item['orderItem']->id,
                items: $item['processings'],
            );
        }
    }
}

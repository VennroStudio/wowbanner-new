<?php

declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Command\OrderSection\Create\CreateOrderSectionCommand;
use App\Modules\Order\Command\OrderSection\Create\CreateOrderSectionHandler;
use App\Modules\Order\Command\OrderSection\Delete\DeleteOrderSectionCommand;
use App\Modules\Order\Command\OrderSection\Delete\DeleteOrderSectionHandler;
use App\Modules\Order\Command\OrderSection\Update\UpdateOrderSectionCommand;
use App\Modules\Order\Command\OrderSection\Update\UpdateOrderSectionHandler;
use App\Modules\Order\Entity\OrderSection\OrderSectionRepository;
use App\Modules\Order\ReadModel\OrderSection\OrderSectionItem;

final readonly class OrderSectionSyncerService
{
    public function __construct(
        private OrderSectionRepository $repository,
        private CreateOrderSectionHandler $createHandler,
        private UpdateOrderSectionHandler $updateHandler,
        private DeleteOrderSectionHandler $deleteHandler,
    ) {}

    /**
     * @param list<OrderSectionItem> $items
     */
    public function sync(int $orderId, array $items): void
    {
        $currentItems = $this->repository->findByOrderId($orderId);
        $currentIds = array_map(static fn($item) => $item->id, $currentItems);
        $commandIds = array_filter(array_map(static fn(OrderSectionItem $item) => $item->id, $items));

        foreach ($currentItems as $currentItem) {
            if (!in_array($currentItem->id, $commandIds, true)) {
                $this->deleteHandler->handle(new DeleteOrderSectionCommand($currentItem->id));
            }
        }

        foreach ($items as $item) {
            if ($item->id !== null && in_array($item->id, $currentIds, true)) {
                $this->updateHandler->handle(new UpdateOrderSectionCommand(
                    id: $item->id,
                    sectionType: $item->sectionType,
                ));
                continue;
            }

            $this->createHandler->handle(new CreateOrderSectionCommand(
                orderId: $orderId,
                sectionType: $item->sectionType,
            ));
        }
    }
}
